#!/bin/bash

# Script de actualización desde GitHub
echo "🔄 Iniciando actualización desde GitHub..."

# Verificar si estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz de Laravel."
    exit 1
fi

# Crear backup
echo "💾 Creando backup..."
backup_dir="../backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$backup_dir"
cp -r app "$backup_dir/"
cp -r resources "$backup_dir/"
cp -r routes "$backup_dir/"
cp -r database "$backup_dir/"
cp -r config "$backup_dir/"
echo "✅ Backup creado en: $backup_dir"

# Actualizar desde Git
echo "📥 Actualizando desde GitHub..."
git fetch origin
git reset --hard origin/main

# Instalar dependencias
echo "📦 Instalando dependencias..."
composer install --optimize-autoloader --no-dev

# Compilar assets
echo "🎨 Compilando assets..."
npm install
npm run build

# Limpiar cache
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# Verificar instalación
echo "✅ Verificando instalación..."
php artisan about

echo "🎉 ¡Actualización completada!"
echo "📝 Información:"
echo "   - Backup guardado en: $backup_dir"
echo "   - Repositorio: https://github.com/agape14/rifas.git"
echo "   - Última actualización: $(date)"
