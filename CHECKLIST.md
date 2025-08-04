# ✅ Lista de Verificación para Despliegue en Hostinger

## 📋 Antes del Despliegue

### **Preparación Local**
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
- [ ] Todos los archivos subidos via FTP/File Manager
- [ ] Contenido de `public/` movido a `public_html/`
- [ ] Resto de archivos un nivel arriba
- [ ] `.env` configurado con datos correctos

### **Configuración del Servidor**
- [ ] Permisos de carpetas configurados (755 para storage/)
- [ ] `.env` con permisos 644
- [ ] Enlace simbólico de storage creado

### **Comandos Ejecutados**
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
