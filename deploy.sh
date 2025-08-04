#!/bin/bash

# Script de despliegue para Hostinger
echo "🚀 Iniciando despliegue en Hostinger..."

# 1. Limpiar cache y optimizar
echo "📦 Optimizando aplicación..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 2. Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Verificar permisos
echo "🔐 Configurando permisos..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env

# 4. Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# 5. Crear enlace simbólico para storage
echo "🔗 Creando enlace de storage..."
php artisan storage:link

# 6. Verificar instalación
echo "✅ Verificando instalación..."
php artisan about

echo "🎉 ¡Despliegue completado!"
echo "📝 Recuerda:"
echo "   - Verificar que APP_DEBUG=false en .env"
echo "   - Configurar la base de datos correctamente"
echo "   - Probar el login de administrador"
echo "   - Verificar que las imágenes se suban"
