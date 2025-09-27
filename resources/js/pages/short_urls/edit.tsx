import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { LoaderCircle, Shield } from 'lucide-react';
import { Label } from '@/components/ui/label';
import { useState } from 'react';
import axios from 'axios';
import { useCsrf } from '@/hooks/useCsrf';
import { sanitizeUrl } from '@/lib/sanitize';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Edit Shortcut',
        href: '/short_urls'
    },
];

interface UrlShortener {
    id: number;
    short_url: string;
    original_url: string;
    visits: number;
    user_id: number;
}

export default function Edit({ urlShortener }: { urlShortener: UrlShortener }) {
    const [originalUrl, setOriginalUrl] = useState(urlShortener.original_url);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState<string | null>(null);
    const { csrfToken, loading: csrfLoading } = useCsrf();
    
    const handleUpdate = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        
        if (!csrfToken) {
            setError('Error de seguridad: Token CSRF no disponible');
            return;
        }
        
        const sanitizedUrl = sanitizeUrl(originalUrl);
        if (!sanitizedUrl) {
            setError('Por favor ingresa una URL válida');
            return;
        }
        
        try {
            setLoading(true);
            setError(null);
            setSuccess(null);
            
            const headers = {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            };
            
            const response = await axios.put(`/api/shortcuts/${urlShortener.id}`, {
                original_url: sanitizedUrl
            }, { headers });
            
            if (response.data.success) {
                setSuccess('Shortcut actualizado exitosamente!');
                setTimeout(() => {
                    router.visit('/short_urls');
                }, 1500);
            } else {
                setError(response.data.message || 'Failed to update shortcut');
            }
        } catch (err: any) {
            console.error('Error updating shortcut:', err);
            
            if (err.response?.status === 419) {
                setError('Error de seguridad: Token CSRF expirado. Por favor recarga la página.');
            } else if (err.response?.data?.errors) {
                setError(Object.values(err.response.data.errors)[0] as string);
            } else {
                setError('Error updating shortcut');
            }
        } finally {
            setLoading(false);
        }
    }
    
    if (csrfLoading) {
        return (
            <AppLayout breadcrumbs={breadcrumbs}>
                <Head title="Edit Shortcut" />
                <div className="flex items-center justify-center min-h-[60vh]">
                    <div className="text-center">
                        <Shield className="h-12 w-12 animate-pulse mx-auto text-blue-500 mb-4" />
                        <p className="text-gray-600">Inicializando medidas de seguridad...</p>
                    </div>
                </div>
            </AppLayout>
        );
    }
    
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Shortcut" />
            <div className="w-8/12 p-4">
                {error && (
                    <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
                        <Shield className="h-5 w-5 mr-2" />
                        {error}
                    </div>
                )}
                
                {success && (
                    <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
                        <Shield className="h-5 w-5 mr-2" />
                        {success}
                    </div>
                )}
                
                <div className="mb-6">
                    <div className="flex items-center mb-2">
                        <Shield className="h-5 w-5 text-green-500 mr-2" />
                        <h2 className="text-lg font-semibold">Editar Shortcut Seguro</h2>
                    </div>
                    <p className="text-sm text-gray-600">
                        Tu URL será validada y sanitizada automáticamente.
                    </p>
                </div>
                
                <form onSubmit={handleUpdate} className="space-y-4">
                    <div className="gap-1.5">
                        <Label htmlFor="original_url">Original URL</Label>
                        <Input 
                            type="url"
                            placeholder="https://www.ejemplo.com" 
                            value={originalUrl}
                            onChange={(e) => setOriginalUrl(e.target.value)}
                            required
                            disabled={loading}
                        />
                        <p className="text-xs text-gray-500 mt-1">
                            Short URL: {urlShortener.short_url}
                        </p>
                    </div>
                    
                    <Button 
                        type="submit" 
                        disabled={loading || !originalUrl.trim()}
                        className="w-full"
                    >
                        {loading && <LoaderCircle className="h-4 w-4 animate-spin mr-2" />}
                        <Shield className="h-4 w-4 mr-2" />
                        Actualizar Shortcut
                    </Button>
                </form>
            </div>
        </AppLayout>
    );
}
