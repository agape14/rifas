#!/bin/bash

# Script de despliegue para Hostinger con Git
echo "🚀 Iniciando despliegue en Hostinger desde Git..."

# Verificar si estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz de Laravel."
    exit 1
fi

# 0. Clonar desde Git (si no existe)
if [ ! -d ".git" ]; then
    echo "📥 Clonando desde GitHub..."
    git clone https://github.com/agape14/rifas.git temp_clone
    mv temp_clone/* .
    mv temp_clone/.* . 2>/dev/null || true
    rm -rf temp_clone
fi

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

# 7. Mostrar información del repositorio
echo "📋 Información del repositorio:"
echo "   - Repositorio: https://github.com/agape14/rifas.git"
echo "   - Para actualizaciones futuras, ejecuta este script nuevamente"

echo "🎉 ¡Despliegue completado!"
echo "📝 Recuerda:"
echo "   - Verificar que APP_DEBUG=false en .env"
echo "   - Configurar la base de datos correctamente"
echo "   - Probar el login de administrador"
echo "   - Verificar que las imágenes se suban"
echo "   - Para actualizaciones: git pull origin main"
