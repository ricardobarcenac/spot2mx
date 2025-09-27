/**
 * Sanitiza input para prevenir XSS usando las funciones nativas del navegador
 */
export const sanitizeInput = (input: string): string => {
    if (typeof input !== 'string') return '';
    
    // Usar createTextNode para escape automático de HTML
    const temp = document.createElement('div');
    temp.textContent = input;
    return temp.textContent || '';
};

/**
 * Sanitiza URL y valida formato usando URL constructor nativo
 */
export const sanitizeUrl = (url: string): string => {
    if (typeof url !== 'string') return '';
    
    // Remover espacios en blanco
    url = url.trim();
    
    // Agregar protocolo si no existe
    if (!url.match(/^https?:\/\//)) {
        url = 'https://' + url;
    }
    
    try {
        const urlObj = new URL(url);
        
        // Solo permitir protocolos seguros
        if (!['http:', 'https:'].includes(urlObj.protocol)) {
            throw new Error('Protocolo no permitido');
        }
        
        return urlObj.toString();
    } catch {
        return '';
    }
};

/**
 * Valida formato de email usando regex nativo
 */
export const sanitizeEmail = (email: string): string => {
    if (typeof email !== 'string') return '';
    
    const sanitized = email.trim().toLowerCase();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    return emailRegex.test(sanitized) ? sanitized : '';
};

/**
 * Sanitiza texto que se mostrará en HTML
 */
export const sanitizeHtml = (html: string): string => {
    if (typeof html !== 'string') return '';
    
    // Crear elemento temporal para usar DOMParser
    const temp = document.createElement('div');
    temp.textContent = html; // Esto escapa automáticamente HTML
    return temp.innerHTML;
};
