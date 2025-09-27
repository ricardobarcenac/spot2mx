import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table"
import { Button } from '@/components/ui/button';
import { ArrowRight, LoaderCircle, Pencil, Trash } from 'lucide-react';
import { useState, useEffect } from 'react';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'URL Shortcuts',
        href: '/short_urls',
    },
];

interface ShortUrl {
    id: number;
    short_url: string;
    original_url: string;
    visits: number;
    user_id: number;
    created_at: string;
    updated_at: string;
}

export default function Index({ flash: initialFlash }: { flash?: { success?: string } }) {
    const { delete: destroy, processing } = useForm();
    const [showMessage, setShowMessage] = useState(true);
    const [shortUrls, setShortUrls] = useState<ShortUrl[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [flash, setFlash] = useState(initialFlash || null);

    // Cargar shortcuts desde la API
    useEffect(() => {
        const fetchShortcuts = async () => {
            try {
                setLoading(true);
                const response = await axios.get('/api/shortcuts');
                
                if (response.data.success) {
                    setShortUrls(response.data.data.shortcuts);
                } else {
                    setError('Failed to load shortcuts');
                }
            } catch (err) {
                setError('Error loading shortcuts');
                console.error('Error fetching shortcuts:', err);
            } finally {
                setLoading(false);
            }
        };

        fetchShortcuts();
    }, []);

    // Verificar si hay mensaje de éxito en localStorage
    useEffect(() => {
        const successMessage = localStorage.getItem('successMessage');
        if (successMessage) {
            setFlash({ success: successMessage });
            localStorage.removeItem('successMessage'); // Limpiar después de usar
        }
    }, []);

    useEffect(() => {
        if (flash?.success) {
            setShowMessage(true);
            const timer = setTimeout(() => {
                setShowMessage(false);
                setFlash(null); // Limpiar el flash después de 10 segundos
            }, 10000); // 10 segundos

            return () => clearTimeout(timer);
        }
    }, [flash?.success]);

    // Actualizar flash cuando cambie el flash inicial (de create/edit)
    useEffect(() => {
        if (initialFlash?.success) {
            setFlash(initialFlash);
        }
    }, [initialFlash]);

    const handleDelete = async (id: number) => {
        if(confirm('Are you sure you want to delete this shortcut?')) {
            try {
                const response = await axios.delete(`/api/shortcuts/${id}`);
                
                if (response.data.success) {
                    // Remover el shortcut de la lista local
                    setShortUrls(prev => prev.filter(url => url.id !== id));
                    
                    // Mostrar mensaje de éxito temporal
                    setFlash({ success: response.data.message });
                    setTimeout(() => setFlash(null), 5000);
                } else {
                    setError('Failed to delete shortcut');
                }
            } catch (err) {
                setError('Error deleting shortcut');
                console.error('Error deleting shortcut:', err);
            }
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="URL Shortcuts" />

            {flash?.success && showMessage && (
                <div className={`bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded my-4 mx-4 transition-opacity duration-1000 ${
                    showMessage ? 'opacity-100' : 'opacity-0'
                }`}>
                    {flash.success}
                </div>
            )}

            {error && (
                <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded my-4 mx-4">
                    {error}
                </div>
            )}

            <div className="m-4">
                <Link href="/short_urls/create">
                    <Button className="btn btn-primary mb-4">
                        Create Shortcut
                    </Button>
                </Link>

                {loading ? (
                    <div className="flex justify-center items-center py-8">
                        <LoaderCircle className="h-8 w-8 animate-spin" />
                        <span className="ml-2">Loading shortcuts...</span>
                    </div>
                ) : shortUrls.length > 0 ? (
                    <Table>
                        <TableCaption>A list of your recent shortcuts created.</TableCaption>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="text-center space-x-2">Id</TableHead>
                                <TableHead className="text-center space-x-2">Code</TableHead>
                                <TableHead className="text-center space-x-2">Original URL</TableHead>
                                <TableHead className="text-center space-x-2">Visits</TableHead>
                                <TableHead className="text-center space-x-2">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {shortUrls.map((shortUrl) => (
                                <TableRow key={shortUrl.id}>
                                    <TableCell className="text-center space-x-2">{shortUrl.id}</TableCell>
                                    <TableCell className="text-center space-x-2">{shortUrl.short_url}</TableCell>
                                    <TableCell className="text-center space-x-2">{shortUrl.original_url}</TableCell>
                                    <TableCell className="text-center space-x-2">{shortUrl.visits}</TableCell>
                                    <TableCell className="text-center space-x-2">
                                        <Button 
                                            className="bg-slate-500 hover:bg-slate-700"
                                            onClick={() => window.open(`/short_urls/shortcut/${shortUrl.short_url}`, '_blank')}
                                        >
                                            <ArrowRight className="h-4 w-4" />
                                        </Button>
                                        <Link href={`/short_urls/edit/${shortUrl.id}`}>
                                            <Button className="bg-slate-500 hover:bg-slate-700">
                                                <Pencil className="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button 
                                            disabled={processing}
                                            className="bg-red-500 hover:bg-red-700" 
                                            onClick={() => handleDelete(shortUrl.id)} 
                                        >
                                            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                            <Trash className="h-4 w-4" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                ) : (
                    <div className="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded my-4">
                        <p className="font-medium">You don't have any shortcuts created yet</p>
                        <p className="text-sm mt-1">Create your first shortcut using the button above to start shortening URLs.</p>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
