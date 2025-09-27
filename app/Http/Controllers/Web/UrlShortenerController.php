<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UrlShortener;
use App\Http\Requests\StoreUrlShortenerRequest;
use App\Http\Requests\UpdateUrlShortenerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UrlShortenerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return inertia('short_urls/index', [
            'flash' => [
                'success' => $request->query('success') ?? session('success')
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('short_urls/create', [
            'shortUrls' => new UrlShortener()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUrlShortenerRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $validated['short_url'] = $this->generateShortUrl();

        UrlShortener::create($validated);

        return redirect()->route('short_urls.index')->with('success', 'Shortcut created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UrlShortener $urlShortener)
    {
        return inertia('short_urls/edit', [
            'urlShortener' => $urlShortener
        ]);
    }

    /**
     * Show redirect page for short URL
     */
    public function redirect($shortUrl)
    {
        return inertia('short_urls/redirect', [
            'shortUrl' => $shortUrl
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUrlShortenerRequest $request, UrlShortener $urlShortener)
    {
        $validated = $request->validated();
        $urlShortener->update($validated);

        return redirect()->route('short_urls.index')->with('success', 'Shortcut updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UrlShortener $urlShortener)
    {
        $urlShortener->update(['status' => 3]);

        return redirect()->route('short_urls.index')->with('success', 'Shortcut deleted successfully');
    }

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
