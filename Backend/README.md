# Studio Torres - Backend API

API REST para sistema de reservas de barbería "Studio Torres". Backend desarrollado en Laravel 8 LTS con PHP 8.0+, MySQL y autenticación Sanctum.

## 🚀 Stack Tecnológico

- **Laravel 8 LTS** - Framework PHP
- **PHP 8.0+** - Lenguaje (compatible con tu versión 8.0.30)
- **MySQL 8** - Base de datos
- **Laravel Sanctum** - Autenticación API token-based
- **Composer** - Gestión de dependencias

## 📋 Requisitos

- PHP >= 8.0
- Composer
- MySQL >= 8.0
- Extensiones PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## 🛠️ Instalación Local

### 1. Clonar repositorio e instalar dependencias

```bash
cd Backend
composer install
```

### 2. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configurar base de datos

Editar `.env` con credenciales MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studio_torres
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

Esto creará:
- ✅ Admin user: `admin@studiotorres.com` / `Admin123!`
- ✅ 2 servicios (Corte 30min, Corte+Barba 60min)
- ✅ 3 imágenes placeholder en galería

### 5. Configurar storage

```bash
php artisan storage:link
```

### 6. Iniciar servidor

```bash
php artisan serve
```

API disponible en: `http://127.0.0.1:8000`

## 🔗 Endpoints API

### Públicos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/services` | Lista servicios activos |
| GET | `/api/services/{id}` | Detalle servicio |
| GET | `/api/gallery` | Imágenes galería |
| GET | `/api/availability?date=YYYY-MM-DD&service_id=1` | Horarios disponibles |
| POST | `/api/appointments` | Crear turno |
| GET | `/api/appointments/{public_code}` | Ver turno |

### Admin (requieren token)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/admin/login` | Login admin |
| POST | `/api/admin/logout` | Logout |
| GET | `/api/admin/dashboard/stats` | Estadísticas |
| GET | `/api/admin/appointments` | Lista turnos (filtros) |
| PUT | `/api/admin/appointments/{id}/status` | Actualizar estado |
| POST | `/api/admin/services` | Crear servicio |
| PUT | `/api/admin/services/{id}` | Actualizar servicio |
| DELETE | `/api/admin/services/{id}` | Eliminar servicio |
| POST | `/api/admin/gallery` | Subir imagen |
| DELETE | `/api/admin/gallery/{id}` | Eliminar imagen |

## 📝 Formato de Respuestas

### Éxito
```json
{
  "ok": true,
  "data": {
    "appointment": { ... },
    "message": "Turno reservado exitosamente"
  }
}
```

### Error
```json
{
  "ok": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Error de validación",
    "details": { ... }
  }
}
```

## 🔐 Autenticación Admin

1. **Login**:
```bash
POST /api/admin/login
{
  "email": "admin@studiotorres.com",
  "password": "Admin123!"
}
```

Respuesta:
```json
{
  "ok": true,
  "data": {
    "token": "1|xyz...",
    "user": { ... }
  }
}
```

2. **Usar token en requests**:
```
Authorization: Bearer 1|xyz...
```

## 🎯 Lógica de Disponibilidad

- **Horario laboral**: 10:00 - 20:00 (configurable en `.env`)
- **Slots**: Cada 30 minutos
- **Servicios**: 
  - Corte: 30 min (ocupa 1 slot)
  - Corte+Barba: 60 min (ocupa 2 slots consecutivos)
- **Validación**: Previene solapamientos automáticamente

### Configuración de horarios

En `.env`:
```env
BUSINESS_START_HOUR=10
BUSINESS_END_HOUR=20
SLOT_INTERVAL_MINUTES=30
```

## 📦 Deploy en Hostinger

### Preparación Local

1. **Generar APP_KEY**:
```bash
php artisan key:generate --show
```

2. **Optimizar para producción**:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### En Hostinger

#### 1. Subir archivos

- Subir todo el contenido de `Backend/` vía FTP/File Manager
- **Importante**: Mover contenido de `public/` a `public_html/`
- Todo lo demás (app, database, etc.) fuera de `public_html/`

Estructura en Hostinger:
```
/home/usuario/
├── public_html/          (contenido de public/)
│   ├── index.php
│   └── .htaccess
├── domains/studiotorres.com/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── ...
```

#### 2. Modificar public_html/index.php

```php
<?php

require __DIR__.'/../domains/studiotorres.com/vendor/autoload.php';

$app = require_once __DIR__.'/../domains/studiotorres.com/bootstrap/app.php';

// ... resto del código
```

#### 3. Configurar .env en Hostinger

Crear `.env` en `/domains/studiotorres.com/`:

```env
APP_NAME="Studio Torres"
APP_ENV=production
APP_KEY=base64:TU_APP_KEY_GENERADA
APP_DEBUG=false
APP_URL=https://tudomain.com

# MySQL Hostinger
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_studio_torres
DB_USERNAME=u123456789_studio_torres
DB_PASSWORD=tu_password_mysql

# CORS Frontend
FRONTEND_URL=https://tudomain.com
FRONTEND_PRODUCTION_URL=https://tudomain.com

# Sanctum
SANCTUM_STATEFUL_DOMAINS=tudomain.com

# Admin
ADMIN_EMAIL=admin@studiotorres.com
ADMIN_PASSWORD=TuPasswordSeguro!
```

#### 4. Ejecutar migraciones

Via SSH o PHP terminal en Hostinger:
```bash
cd domains/studiotorres.com
php artisan migrate --force
php artisan db:seed --force
```

#### 5. Configurar permisos

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

#### 6. Storage link

```bash
php artisan storage:link
```

O crear manualmente:
```bash
ln -s ../domains/studiotorres.com/storage/app/public public_html/storage
```

#### 7. .htaccess en public_html

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Si no funciona, probar:
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Verificar Deploy

1. **Healthcheck**: `https://tudomain.com/up`
2. **Test API**: `https://tudomain.com/api/services`
3. **Admin login**: `POST https://tudomain.com/api/admin/login`

## 🐛 Troubleshooting

### Error 500 al iniciar
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Limpiar caché
php artisan cache:clear
php artisan config:clear
```

### Storage no funciona
```bash
# Re-link storage
php artisan storage:link
# O manual:
ln -s ../storage/app/public public/storage
```

### CORS errors
Verificar en `.env`:
- `FRONTEND_URL` coincida con URL del frontend
- `SANCTUM_STATEFUL_DOMAINS` incluya dominio

### Permisos Hostinger
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R usuario:usuario storage
```

## 📊 Base de Datos

### Tablas

- `users` - Admins
- `services` - Servicios (corte, barba)
- `appointments` - Turnos reservados
- `gallery_items` - Imágenes galería

### Estados de Appointments

- `PENDING` - Pendiente confirmación
- `CONFIRMED` - Confirmado
- `CANCELLED` - Cancelado
- `DONE` - Completado

## 🔧 Configuración Avanzada

### Cambiar horarios laborales

`.env`:
```env
BUSINESS_START_HOUR=9   # Abre 9 AM
BUSINESS_END_HOUR=21    # Cierra 9 PM
SLOT_INTERVAL_MINUTES=15 # Slots cada 15 min
```

### Email notifications (opcional)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@tudomain.com
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudomain.com
```

## 🧪 Testing

```bash
php artisan test
```

## 📱 Integración con Frontend

El frontend Vue debe configurar en `.env`:

```env
VITE_API_BASE_URL=https://tudomain.com/api
```

Y en axios/fetch:
```js
headers: {
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'application/json'
}
```

## 🛡️ Seguridad

- ✅ Validación con Form Requests
- ✅ Sanctum token-based auth
- ✅ CORS configurado
- ✅ SQL injection protection (Eloquent)
- ✅ XSS protection
- ✅ CSRF protection

## 📄 Licencia

MIT

## 👤 Autor

Studio Torres - Sistema de reservas para barbería profesional

---

**¿Problemas?** Revisa logs en `storage/logs/laravel.log`
