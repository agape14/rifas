# ✅ Lista de Verificación para Despliegue en Hostinger

## 📋 Antes del Despliegue

### **Preparación Local (Opcional)**
- [ ] `git clone https://github.com/agape14/rifas.git` ejecutado
- [ ] `composer install --optimize-autoloader --no-dev` ejecutado
- [ ] `php artisan key:generate` ejecutado
- [ ] `npm run build` ejecutado
- [ ] Archivos `.env` configurado para producción
- [ ] `.htaccess` optimizado para Hostinger

### **Base de Datos**
- [ ] Base de datos creada en Hostinger
- [ ] Credenciales de BD anotadas
- [ ] Host de BD verificado (generalmente localhost)

## 🚀 Durante el Despliegue

### **Subida de Archivos**
- [ ] Repositorio clonado desde GitHub: `https://github.com/agape14/rifas.git`
- [ ] Archivos movidos a la ubicación correcta (public/ → public_html/)
- [ ] Dependencias instaladas (`composer install --optimize-autoloader --no-dev`)
- [ ] Assets compilados (`npm install && npm run build`)
- [ ] `.env` configurado con datos correctos

### **Configuración del Servidor**
- [ ] Permisos de carpetas configurados (755 para storage/)
- [ ] `.env` con permisos 644
- [ ] Enlace simbólico de storage creado

### **Comandos Ejecutados**
- [ ] `git clone https://github.com/agape14/rifas.git` (si es primera vez)
- [ ] `composer install --optimize-autoloader --no-dev`
- [ ] `npm install && npm run build`
- [ ] `php artisan migrate --force`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan storage:link`

## ✅ Después del Despliegue

### **Verificación del Sitio**
- [ ] Sitio web carga correctamente
- [ ] No hay errores 500
- [ ] CSS y JS cargan correctamente
- [ ] Imágenes se muestran

### **Funcionalidades**
- [ ] Login de administrador funciona
- [ ] CRUD de usuarios funciona
- [ ] Subida de imágenes funciona
- [ ] Sorteo funciona correctamente
- [ ] Tema oscuro/claro funciona

### **Seguridad**
- [ ] `APP_DEBUG=false` en `.env`
- [ ] HTTPS configurado
- [ ] `.env` no es accesible públicamente
- [ ] Logs funcionan correctamente

### **Optimización**
- [ ] Cache configurado
- [ ] Assets compilados
- [ ] Base de datos optimizada
- [ ] Permisos correctos

## 🔧 Problemas Comunes

### **Error 500**
- [ ] Verificar logs en `storage/logs/`
- [ ] Verificar permisos de carpetas
- [ ] Verificar configuración de `.env`

### **Error de Base de Datos**
- [ ] Verificar credenciales en `.env`
- [ ] Verificar que la BD existe
- [ ] Ejecutar `php artisan migrate`

### **Assets no cargan**
- [ ] Verificar que `public/build/` existe
- [ ] Ejecutar `npm run build`
- [ ] Verificar rutas en `vite.config.js`

### **Imágenes no se suben**
- [ ] Verificar permisos de `storage/app/public/`
- [ ] Ejecutar `php artisan storage:link`
- [ ] Verificar configuración de `FILESYSTEM_DISK`

## 📞 Contacto de Soporte

Si tienes problemas:
1. Revisa los logs en `storage/logs/`
2. Verifica la configuración de PHP en Hostinger
3. Contacta al soporte de Hostinger

## 🎯 URLs Importantes

- **Sitio principal**: https://tudominio.com
- **Panel admin**: https://tudominio.com/admin
- **Login**: https://tudominio.com/login
- **Gestión usuarios**: https://tudominio.com/admin/users 
