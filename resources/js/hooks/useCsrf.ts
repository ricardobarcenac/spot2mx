import { useEffect, useState } from 'react';
import axios from 'axios';

interface CsrfToken {
    token: string;
    expires_at: string;
}

export const useCsrf = () => {
    const [csrfToken, setCsrfToken] = useState<string>('');
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const getCsrfToken = async () => {
            try {
                const response = await axios.get('/csrf-token');
                const data: CsrfToken = response.data;
                
                setCsrfToken(data.token);
                
                // Configurar el token por defecto en axios para todas las requests
                axios.defaults.headers.common['X-CSRF-TOKEN'] = data.token;
                
                // Configurar interceptor para manejar token expirado
                axios.interceptors.response.use(
                    (response) => response,
                    async (error) => {
                        if (error.response?.status === 419) { // CSRF token mismatch
                            console.warn('CSRF token expirado, renovando...');
                            const newToken = await fetchCsrfToken();
                            if (newToken) {
                                // Reintentar la request original
                                error.config.headers['X-CSRF-TOKEN'] = newToken;
                                return axios.request(error.config);
                            }
                        }
                        return Promise.reject(error);
                    }
                );
            } catch (error) {
                console.error('Error obteniendo CSRF token:', error);
            } finally {
                setLoading(false);
            }
        };

        getCsrfToken();
    }, []);

    const fetchCsrfToken = async (): Promise<string | null> => {
        try {
            const response = await axios.get('/csrf-token');
            const data: CsrfToken = response.data;
            const token = data.token;
            
            setCsrfToken(token);
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            return token;
        } catch (error) {
            console.error('Error renovando CSRF token:', error);
            return null;
        }
    };

    return { csrfToken, loading, refreshToken: fetchCsrfToken };
};
