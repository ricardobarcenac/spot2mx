# Spot2MX - Acortador de URLs
Technical Challenge Spot2 para acortar URLs construida con Laravel 12 y React 19.

## 🚀 Características

- Acortamiento de URLs: Convierte URLs largas en enlaces cortos y manejables
- Gestión de enlaces: CRUD completo para URLs acortadas
- Autenticación completa: Sistema de login/registro con Laravel Fortify
- API REST: Endpoints para integración con aplicaciones externas
- Documentación API: Swagger/OpenAPI integrado
- Componentes UI modernos: Basados en Radix UI y Tailwind CSS

## 🛠️ Stack Tecnológico

### Backend
- Laravel 12 - Framework PHP
- Laravel Fortify - Autenticación
- MySQL - Base de datos (configurable)
- Swagger/OpenAPI - Documentación de API

### Frontend
- React y TypeScript - Biblioteca de UI
- Tailwind CSS 4 - CSS
- Lucide React - Iconos
- Vite - Tool

## 📋 Requisitos de Sistema

- PHP 8.2+
- Composer
- Node.js 18+
- npm o yarn

## 🚀 Instalación

### 1. Configurar base de datos
- Ejecutar migraciones
php artisan migrate

### 2. Compilar assets

### Para desarrollo
npm run dev

### Para producción
npm run build

## 🏃‍♂️ Ejecutar la Aplicación

composer run dev

## 🔧 Comandos Útiles

### Laravel
Migraciones: php artisan migrate

### Cache
- php artisan cache:clear
- php artisan config:clear
- php artisan route:clear

## 🌐 Rutas Principales

### Web Routes

- Página de inicio
- /dashboard - Dashboard principal
- /short\_urls - Lista de URLs acortadas
- /short\_urls/create - Crear nueva URL
- /short\_urls/edit/{id} - Editar URL
- /short\_urls/shortcut/{short} - Redirección

### API Routes

- GET /api/shortcuts - Listar shortcuts
- POST /api/shortcuts - Crear shortcut
- PUT /api/shortcuts/{id} - Actualizar shortcut
- DELETE /api/shortcuts/{id} - Eliminar shortcut

## 📚 Documentación API
La documentación de la API está disponible en Swagger UI: http://18.216.237.66/api/documentation

## 🎨 Personalización

### Componentes UI
Los componentes están en resources/js/components/ui/ y siguen el patrón de Radix UI con Tailwind CSS.

### Estilos

- Tailwind CSS 4 para estilos
- CSS Variables para temas
- Responsive design por defecto

### 🚀 Despliegue
Variables de Entorno Importantes

- env
- APP\_NAME="Spot2MX"
- APP\_URL=https://localhost:8000 (generado al ejecutar el proyecto)
- DB\_CONNECTION=mysql
- DB\_DATABASE=nombre\_bbdd

### Pasos de Despliegue

- 1\. Configurar servidor web (Apache)
- 2\. Instalar dependencias: composer install --no-dev
- 3\. Compilar assets: npm run build
- 4\. Ejecutar migraciones: php artisan migrate --force
- 5\. Configurar permisos de storage y cache
- 6\. Eliminar el archivo hot (ubicado en /public)

### ¿Cómo funciona el aplicativo?
- 1\. Registra tus datos (automáticamente iniciará sesión)
- 2\. Ingresa al módulo URL Shortener
- 3\. Registra la URL que deseas acortar
- 4\. Si cumple con las condiciones mínimas, se registrará y se generará un código
- 5\. Para probar el shortcut tienes un botón en la columna de acciones para realizar la redirección (cargará una pantalla previa donde te indicará que serás redirigido)
