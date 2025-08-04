# 🚀 Guía de Despliegue en Hostinger

## 📋 Pasos para subir el proyecto a Hostinger

### **1. Preparación Local**

```bash
# Instalar dependencias de producción
composer install --optimize-autoloader --no-dev

# Generar clave de aplicación
php artisan key:generate

# Compilar assets
npm run build
```

### **2. Crear Base de Datos en Hostinger**

1. Accede al panel de control de Hostinger
2. Ve a "Bases de datos MySQL"
3. Crea una nueva base de datos
4. Anota:
   - Nombre de la base de datos
   - Usuario
   - Contraseña
   - Host (generalmente localhost)

### **3. Subir Archivos**

**Opción A: FTP/SFTP**
1. Conecta via FTP a tu hosting
2. Sube todos los archivos a la carpeta `public_html`
3. **IMPORTANTE**: Mueve el contenido de `public/` a `public_html/`
4. Mueve el resto de archivos un nivel arriba

**Opción B: File Manager**
1. Accede al File Manager de Hostinger
2. Sube los archivos comprimidos
3. Extrae en la carpeta correcta

### **4. Configurar el .env**

Crea un archivo `.env` en la raíz con:

```env
APP_NAME="Sistema de Rifas"
APP_ENV=production
APP_KEY=base64:TU_CLAVE_GENERADA
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### **5. Configurar Permisos**

```bash
# En el servidor, ejecuta:
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
```

### **6. Ejecutar Migraciones**

```bash
# Via SSH o Terminal de Hostinger:
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **7. Configurar Dominio**

1. En el panel de Hostinger, configura tu dominio
2. Asegúrate de que apunte a la carpeta `public_html`

### **8. Verificar Funcionamiento**

1. Visita tu sitio web
2. Verifica que las rutas funcionen
3. Prueba el login de administrador
4. Verifica que las imágenes se suban correctamente

## 🔧 Solución de Problemas Comunes

### **Error 500**
- Verifica permisos de carpetas
- Revisa logs en `storage/logs/`
- Asegúrate de que `.env` esté configurado

### **Error de Base de Datos**
- Verifica credenciales en `.env`
- Asegúrate de que la base de datos existe
- Ejecuta `php artisan migrate`

### **Assets no cargan**
- Ejecuta `npm run build`
- Verifica que `public/build/` existe
- Revisa rutas en `vite.config.js`

### **Imágenes no se suben**
- Verifica permisos de `storage/app/public/`
- Ejecuta `php artisan storage:link`
- Verifica configuración de `FILESYSTEM_DISK`

## 📁 Estructura Final en Hostinger

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

## 🔒 Seguridad

1. **Nunca subas** `.env` con datos sensibles
2. Configura `APP_DEBUG=false` en producción
3. Usa HTTPS siempre
4. Mantén actualizadas las dependencias
5. Haz backups regulares de la base de datos

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs en `storage/logs/`
2. Verifica la configuración de PHP en Hostinger
3. Contacta al soporte de Hostinger si es necesario 
