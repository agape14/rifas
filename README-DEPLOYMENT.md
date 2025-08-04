# 🚀 Despliegue en Hostinger con Git

## 📋 Información del Repositorio

- **Repositorio**: https://github.com/agape14/rifas.git
- **Framework**: Laravel 11
- **Hosting**: Hostinger
- **Método**: Git Clone

## 🎯 Ventajas del Despliegue con Git

✅ **Actualizaciones automáticas** desde GitHub  
✅ **Control de versiones** completo  
✅ **Rollback fácil** si algo sale mal  
✅ **Despliegue más rápido** y profesional  
✅ **Backup automático** antes de actualizar  

## 📦 Archivos de Despliegue

- `DEPLOYMENT.md` - Guía completa de despliegue
- `CHECKLIST.md` - Lista de verificación paso a paso
- `deploy.sh` - Script de despliegue inicial
- `update.sh` - Script de actualización
- `env-production.txt` - Configuración de ejemplo

## 🚀 Comandos Rápidos

### **Despliegue Inicial**
```bash
# En el servidor de Hostinger:
cd public_html
git clone https://github.com/agape14/rifas.git temp_rifas
mv temp_rifas/public/* .
mv temp_rifas/* ../
cd ..
rm -rf temp_rifas
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### **Actualización**
```bash
# En el servidor de Hostinger:
cd public_html
git fetch origin
git reset --hard origin/main
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Usando Scripts**
```bash
# Despliegue inicial
bash deploy.sh

# Actualización
bash update.sh
```

## 🔧 Configuración Requerida

### **1. Base de Datos**
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### **2. Aplicación**
```env
APP_NAME="Sistema de Rifas"
APP_ENV=production
APP_KEY=base64:TU_CLAVE_GENERADA
APP_DEBUG=false
APP_URL=https://tudominio.com
```

## 📁 Estructura en Hostinger

```
public_html/          # Contenido de public/
├── index.php
├── .htaccess
├── css/
├── js/
└── build/

../                   # Resto de archivos Laravel
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── composer.json
```

## 🔄 Flujo de Trabajo

### **Desarrollo Local**
1. Hacer cambios en el código
2. Probar localmente
3. Commit y push a GitHub
4. Ejecutar script de actualización en el servidor

### **Actualización en Producción**
1. Conectar via SSH al servidor
2. Ejecutar `bash update.sh`
3. Verificar que todo funcione
4. Si hay problemas, restaurar desde backup

## 🛠️ Herramientas Necesarias

- **Git** - Para clonar y actualizar
- **Composer** - Para dependencias PHP
- **Node.js/NPM** - Para compilar assets
- **SSH** - Para conectar al servidor

## 🔒 Seguridad

- ✅ `.env` con permisos 644
- ✅ `APP_DEBUG=false` en producción
- ✅ HTTPS configurado
- ✅ Logs en `storage/logs/`
- ✅ Backup automático antes de actualizar

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs en `storage/logs/`
2. Verifica la configuración de PHP en Hostinger
3. Contacta al soporte de Hostinger
4. Revisa el backup si es necesario

## 🎯 URLs Importantes

- **Sitio principal**: https://tudominio.com
- **Panel admin**: https://tudominio.com/admin
- **Login**: https://tudominio.com/login
- **Gestión usuarios**: https://tudominio.com/admin/users
- **Repositorio**: https://github.com/agape14/rifas.git 
