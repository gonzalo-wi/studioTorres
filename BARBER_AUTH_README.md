# Sistema de Autenticación de Barberos

Este documento describe el sistema de autenticación para barberos implementado en la aplicación.

## Características

- **Login independiente**: Los barberos tienen su propia página de login (`/barber/login`)
- **Panel exclusivo**: Dashboard personalizado con estadísticas y gestión de turnos
- **Gestión de turnos**: Los barberos pueden confirmar y cancelar sus propios turnos
- **Autenticación segura**: Basada en Laravel Sanctum con tokens Bearer

## Arquitectura

### Backend (Laravel)

#### Migraciones
- `2024_12_16_000001_create_barbers_table.php`: Tabla de barberos con columna `user_id` (relación con usuarios)
- `0001_01_01_000000_create_users_table.php`: Tabla de usuarios con campo `role` (ADMIN, BARBER, CLIENT)

#### Modelos
- `User`: Modelo de usuario con campo `role`
- `Barber`: Modelo de barbero con relación `belongsTo(User::class)`

#### Controladores
- `BarberAuthController`: Maneja login, logout y perfil del barbero
  - `POST /api/barber/login`: Login con email/password
  - `POST /api/barber/logout`: Cierre de sesión
  - `GET /api/barber/me`: Obtener perfil del barbero autenticado

- `BarberController`: Gestión de turnos y estadísticas
  - `GET /api/barber/appointments`: Listar turnos del barbero (con filtros)
  - `PATCH /api/barber/appointments/{id}/confirm`: Confirmar turno
  - `PATCH /api/barber/appointments/{id}/cancel`: Cancelar turno
  - `GET /api/barber/stats`: Estadísticas (hoy, mes, próximos turnos)
  - `GET /api/barber/schedule`: Horarios del barbero

#### Rutas API
Todas las rutas bajo `/api/barber` (excepto `/login`) están protegidas con `auth:sanctum`.

### Frontend (Vue 3)

#### Páginas
- `BarberLoginPage.vue`: Página de login con diseño azul (diferente al admin)
- `BarberDashboard.vue`: Dashboard con estadísticas y próximos turnos
- `BarberAppointments.vue`: Lista completa de turnos con filtros y acciones

#### Layouts
- `BarberLayout.vue`: Layout con sidebar azul para el panel de barberos

#### Composables
- `useBarberAuth.js`: Maneja estado de autenticación, login, logout y perfil

#### Rutas
- `/barber/login`: Login de barberos
- `/barber`: Dashboard (protegido)
- `/barber/appointments`: Gestión de turnos (protegido)

#### API Client
- `apiClient.js`: Modificado para incluir token de barbero en headers para endpoints `/api/barber/*`
- Token almacenado en `localStorage` como `barber_token`

## Usuarios de Prueba

Después de ejecutar las migraciones y seeders, se crean automáticamente dos usuarios de barberos:

### Hernán García
- **Email**: `hernan@studiotorres.com`
- **Password**: `password123`

### Carlos Mendez
- **Email**: `carlos@studiotorres.com`
- **Password**: `password123`

## Instalación y Configuración

### Backend
```bash
cd Backend

# Ejecutar migraciones
php artisan migrate:fresh --seed

# Crear usuarios de barberos (si no están creados)
php artisan db:seed --class=BarberUserSeeder
```

### Frontend
No requiere configuración adicional. Las rutas y componentes se cargan automáticamente.

## Flujo de Autenticación

1. El barbero ingresa a `/barber/login`
2. Introduce su email y contraseña
3. El backend valida:
   - Credenciales correctas
   - Usuario con `role = 'BARBER'`
   - Perfil de barbero asociado (`barbers.user_id`)
4. Si es exitoso, retorna:
   - Token de autenticación (Bearer)
   - Datos del barbero
5. El frontend almacena el token en `localStorage`
6. Todas las peticiones a `/api/barber/*` incluyen el token en headers
7. El backend valida el token con Sanctum middleware

## Diferencias con Admin

| Aspecto | Admin | Barbero |
|---------|-------|---------|
| **Login** | `/admin/login` | `/barber/login` |
| **Token** | `admin_token` | `barber_token` |
| **Color tema** | Blanco/Dorado | Blanco/Azul |
| **Permisos** | Ver todos los turnos | Solo sus turnos |
| **Acciones** | CRUD completo | Confirmar/Cancelar turnos |
| **Estadísticas** | Globales | Personales |

## Crear Nuevos Barberos con Acceso

Para dar acceso a un barbero existente:

```php
// 1. Crear usuario
$user = User::create([
    'name' => 'Nombre Barbero',
    'email' => 'barbero@email.com',
    'password' => Hash::make('password'),
    'role' => 'BARBER'
]);

// 2. Asociar con barbero existente
$barber = Barber::find($barberId);
$barber->user_id = $user->id;
$barber->save();
```

## Seguridad

- ✅ Tokens Bearer JWT con Sanctum
- ✅ Validación de rol en cada request
- ✅ Solo acceso a turnos propios (filtrado por `barber_id`)
- ✅ Tokens separados para admin y barbero
- ✅ Middleware `auth:sanctum` en todas las rutas protegidas
- ✅ Validación de permisos en controladores

## Próximas Mejoras

- [ ] Recuperación de contraseña para barberos
- [ ] Notificaciones push de nuevos turnos
- [ ] Estadísticas avanzadas (ingresos, servicios más solicitados)
- [ ] Gestión de disponibilidad (marcar días libres)
- [ ] Chat con clientes
