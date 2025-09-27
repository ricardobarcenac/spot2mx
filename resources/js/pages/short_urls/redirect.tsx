import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { LoaderCircle, ExternalLink, Clock } from 'lucide-react';
import { useState, useEffect } from 'react';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Redirecting...',
        href: '#'
    },
];

interface RedirectData {
    original_url: string;
    short_url: string;
    visits: number;
}

export default function Redirect({ shortUrl }: { shortUrl: string }) {
    const [redirectData, setRedirectData] = useState<RedirectData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [countdown, setCountdown] = useState(5);

    // Cargar datos de redirección desde la API
    useEffect(() => {
        const fetchRedirectData = async () => {
            try {
                setLoading(true);
                const response = await axios.get(`/api/redirect/${shortUrl}`);
                
                if (response.data.success) {
                    setRedirectData(response.data.data);
                } else {
                    setError('Short URL not found');
                }
            } catch (err: any) {
                if (err.response?.status === 404) {
                    setError('Short URL not found or inactive');
                } else {
                    setError('Error loading redirect data');
                }
                console.error('Error fetching redirect data:', err);
            } finally {
                setLoading(false);
            }
        };

        fetchRedirectData();
    }, [shortUrl]);

    // Countdown y redirección automática
    useEffect(() => {
        if (redirectData && countdown > 0) {
            const timer = setTimeout(() => {
                setCountdown(countdown - 1);
            }, 1000);

            return () => clearTimeout(timer);
                } else if (redirectData && countdown === 0) {
                    // Redireccionar automáticamente en la misma pestaña
                    window.location.href = redirectData.original_url;
                }
    }, [redirectData, countdown]);

    const handleManualRedirect = () => {
        if (redirectData) {
            window.location.href = redirectData.original_url;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Redirecting..." />
            
            <div className="flex items-center justify-center min-h-[60vh]">
                <div className="text-center max-w-md mx-auto p-6">
                    {loading ? (
                        <div className="space-y-4">
                            <LoaderCircle className="h-12 w-12 animate-spin mx-auto text-blue-500" />
                            <h2 className="text-xl font-semibold text-gray-700">
                                Validating short URL...
                            </h2>
                            <p className="text-gray-500">
                                Please wait while we verify the link
                            </p>
                        </div>
                    ) : error ? (
                        <div className="space-y-4">
                            <div className="h-12 w-12 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                                <ExternalLink className="h-6 w-6 text-red-500" />
                            </div>
                            <h2 className="text-xl font-semibold text-red-700">
                                Link Not Found
                            </h2>
                            <p className="text-red-500">
                                {error}
                            </p>
                            <Button 
                                onClick={() => window.location.href = '/short_urls'}
                                className="mt-4"
                            >
                                Go Back
                            </Button>
                        </div>
                    ) : redirectData ? (
                        <div className="space-y-6">
                            <div className="h-16 w-16 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                                <ExternalLink className="h-8 w-8 text-green-500" />
                            </div>
                            
                            <div className="space-y-2">
                                <h2 className="text-2xl font-semibold text-gray-700">
                                    Redirecting...
                                </h2>
                                <p className="text-blue-600 font-medium break-all">
                                    {redirectData.original_url}
                                </p>
                            </div>

                            <div className="space-y-4">
                                <div className="flex items-center justify-center space-x-2">
                                    <Clock className="h-5 w-5 text-gray-500" />
                                        <span className="text-lg font-medium text-gray-700">
                                            Redirecting in {countdown} seconds
                                        </span>
                                </div>
                                
                                <div className="w-full bg-gray-200 rounded-full h-2">
                                    <div 
                                        className="bg-blue-500 h-2 rounded-full transition-all duration-1000"
                                        style={{ width: `${((5 - countdown) / 5) * 100}%` }}
                                    ></div>
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Button 
                                    onClick={handleManualRedirect}
                                    className="w-full bg-blue-500 hover:bg-blue-600"
                                >
                                    <ExternalLink className="h-4 w-4 mr-2" />
                                    Go Now
                                </Button>
                                
                                <Button 
                                    variant="outline"
                                    onClick={() => window.location.href = '/short_urls'}
                                    className="w-full"
                                >
                                    Cancel
                                </Button>
                            </div>
                        </div>
                    ) : null}
                </div>
            </div>
        </AppLayout>
    );
}
