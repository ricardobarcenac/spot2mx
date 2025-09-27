<?php

namespace App\Http\Controllers\Api;

use App\Models\UrlShortener;
use App\Http\Requests\StoreUrlShortenerRequest;
use App\Http\Requests\UpdateUrlShortenerRequest;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Exception;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Spot2MX URL Shortener API",
 *     version="1.0.0",
 *     description="API para acortar URLs y gestionar enlaces cortos",
 *     @OA\Contact(
 *         email="ricardobarcena.c@gmail.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class UrlShortenerController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/shortcuts",
     *     summary="Obtener lista de shortcuts",
     *     description="Retorna todos los shortcuts del usuario autenticado",
     *     operationId="getShortcuts",
     *     tags={"Shortcuts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de shortcuts obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shortcuts retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="count", type="integer", example=5),
     *                 @OA\Property(
     *                     property="shortcuts",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="short_url", type="string", example="abc123"),
     *                         @OA\Property(property="original_url", type="string", example="https://example.com"),
     *                         @OA\Property(property="visits", type="integer", example=10),
     *                         @OA\Property(property="user_id", type="integer", example=1),
     *                         @OA\Property(property="status", type="integer", example=1),
     *                         @OA\Property(property="created_at", type="string", format="datetime"),
     *                         @OA\Property(property="updated_at", type="string", format="datetime")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $shortUrls = UrlShortener::where('status', 1)->get();

            return $this->successResponse([
                'shortcuts' => $shortUrls,
                'count' => $shortUrls->count()
            ], 'Shortcuts retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve shortcuts', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/shortcuts",
     *     summary="Crear nuevo shortcut",
     *     description="Crea un nuevo shortcut para una URL original",
     *     operationId="createShortcut",
     *     tags={"Shortcuts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"original_url"},
     *             @OA\Property(property="original_url", type="string", format="url", example="https://www.google.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Shortcut creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shortcut created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="short_url", type="string", example="abc123"),
     *                 @OA\Property(property="original_url", type="string", example="https://www.google.com"),
     *                 @OA\Property(property="visits", type="integer", example=0),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="original_url", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreUrlShortenerRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = auth()->user()->id;
            $validated['short_url'] = $this->generateShortUrl();

            $urlShortener = UrlShortener::create($validated);

            return $this->successResponse($urlShortener, 'Shortcut created successfully', 201);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to create shortcut', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/shortcuts/{id}",
     *     summary="Obtener shortcut específico",
     *     description="Retorna un shortcut específico por su ID",
     *     operationId="getShortcut",
     *     tags={"Shortcuts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del shortcut",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shortcut obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shortcut retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="short_url", type="string", example="abc123"),
     *                 @OA\Property(property="original_url", type="string", example="https://example.com"),
     *                 @OA\Property(property="visits", type="integer", example=10),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="datetime"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shortcut no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Shortcut not found")
     *         )
     *     )
     * )
     */
    public function show(UrlShortener $urlShortener)
    {
        try {
            return $this->successResponse($urlShortener, 'Shortcut retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve shortcut', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/redirect/{shortUrl}",
     *     summary="Redireccionar shortcut",
     *     description="Obtiene la URL original de un shortcut y incrementa las visitas",
     *     operationId="redirectShortcut",
     *     tags={"Redirect"},
     *     @OA\Parameter(
     *         name="shortUrl",
     *         in="path",
     *         description="Código corto del shortcut",
     *         required=true,
     *         @OA\Schema(type="string", example="abc123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL encontrada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="URL found successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="original_url", type="string", example="https://www.google.com"),
     *                 @OA\Property(property="short_url", type="string", example="abc123"),
     *                 @OA\Property(property="visits", type="integer", example=11)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shortcut no encontrado o inactivo",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Short URL not found or inactive")
     *         )
     *     )
     * )
     */
    public function redirect($shortUrl)
    {
        try {
            $urlShortener = UrlShortener::where('short_url', $shortUrl)
                ->where('status', 1)
                ->first();

            if (!$urlShortener) {
                return $this->notFoundResponse('Short URL not found or inactive');
            }

            // Incrementar las visitas
            $urlShortener->increment('visits');
            
            // Refrescar el modelo para obtener el valor actualizado
            $urlShortener->refresh();

            return $this->successResponse([
                'original_url' => $urlShortener->original_url,
                'short_url' => $urlShortener->short_url,
                'visits' => $urlShortener->visits
            ], 'URL found successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve URL', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/shortcuts/{id}",
     *     summary="Actualizar shortcut",
     *     description="Actualiza un shortcut existente",
     *     operationId="updateShortcut",
     *     tags={"Shortcuts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del shortcut",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"original_url"},
     *             @OA\Property(property="original_url", type="string", format="url", example="https://www.updated-url.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shortcut actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shortcut updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="short_url", type="string", example="abc123"),
     *                 @OA\Property(property="original_url", type="string", example="https://www.updated-url.com"),
     *                 @OA\Property(property="visits", type="integer", example=10),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="datetime"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="original_url", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateUrlShortenerRequest $request, UrlShortener $urlShortener)
    {
        try {
            $validated = $request->validated();
            $urlShortener->update($validated);
            $urlShortener->refresh();

            return $this->successResponse($urlShortener, 'Shortcut updated successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to update shortcut', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/shortcuts/{id}",
     *     summary="Eliminar shortcut",
     *     description="Elimina un shortcut (cambia su status a inactivo)",
     *     operationId="deleteShortcut",
     *     tags={"Shortcuts"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del shortcut",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shortcut eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shortcut deleted successfully"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shortcut no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Shortcut not found")
     *         )
     *     )
     * )
     */
    public function destroy(UrlShortener $urlShortener)
    {
        try {
            $urlShortener->update(['status' => 3]);

            return $this->successResponse(null, 'Shortcut deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete shortcut', 500);
        }
    }

    /**
     * Generar un código corto único para la URL
     */
    private function generateShortUrl()
    {
        $shortUrl = Str::random(6);

        $urlShortener = UrlShortener::where('short_url', $shortUrl)->first();

        if ($urlShortener) {
            return $this->generateShortUrl();
        }

        return strtolower($shortUrl);
    }
}