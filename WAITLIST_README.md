# 🔔 Sistema de Lista de Espera Inteligente

## Descripción

Sistema automático que notifica a clientes cuando se libera un turno cancelado, maximizando la ocupación de la barbería.

## 🎯 Características

- ✅ **Notificación automática** por email cuando se cancela un turno
- ✅ **Preferencias flexibles**: Rango horario opcional
- ✅ **Ventana de confirmación**: 2 horas para confirmar antes de ofrecer al siguiente
- ✅ **Prioridad FIFO**: Primero en lista, primero en ser notificado
- ✅ **Auto-expiración**: Las entradas expiran después de 7 días
- ✅ **Conversión automática**: Convierte entrada de waitlist en turno confirmado

## 📋 Flujo Completo

```mermaid
1. Cliente busca turno → No disponible
2. Se muestra WaitlistModal
3. Cliente completa formulario (nombre, teléfono, email, preferencias)
4. Backend guarda en tabla `waitlist`
5. Otro cliente cancela turno → AppointmentService detecta cancelación
6. Se dispara ProcessCancelledAppointmentJob
7. WaitlistService busca coincidencias (fecha, servicio, horario)
8. Se encuentra match → Envía email con SlotAvailableNotification
9. Cliente recibe email con link único para confirmar
10. Cliente hace click → Confirma turno en 2 horas
11. Waitlist entry se marca como CONVERTED
12. Si no confirma → Status vuelve a WAITING y se ofrece al siguiente
```

## 🗄️ Estructura de Base de Datos

### Tabla `waitlist`

```sql
- id (PK)
- client_name
- client_phone
- client_email (nullable)
- service_id (FK → services)
- preferred_date (date)
- preferred_time_start (time, nullable) -- Ej: 10:00
- preferred_time_end (time, nullable)   -- Ej: 14:00
- barber_id (FK → barbers, nullable)
- status (ENUM: 'WAITING', 'NOTIFIED', 'CONVERTED', 'EXPIRED')
- notified_at (timestamp, nullable)
- expires_at (timestamp, nullable)
- created_at, updated_at
```

## 🔧 Configuración

### 1. Variables de Entorno (.env)

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=studio.torres.ok@gmail.com
MAIL_PASSWORD="chjr bhqe vwkn rxcn"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="studio.torres.ok@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

# Frontend URL (para links en emails)
FRONTEND_URL=http://localhost:5173

# Waitlist Config
WAITLIST_EXPIRY_DAYS=7
WAITLIST_NOTIFICATION_WINDOW_HOURS=2

# Queue (para Jobs)
QUEUE_CONNECTION=database
```

### 2. Ejecutar Migraciones

```bash
php artisan migrate
```

### 3. Configurar Queue Worker (Producción)

Para que los Jobs se ejecuten automáticamente:

```bash
# Opción 1: Queue Worker (recomendado)
php artisan queue:work --sleep=3 --tries=3

# Opción 2: Supervisor (producción)
[program:laravel-worker]
command=php /path/to/artisan queue:work --sleep=3 --tries=3
```

### 4. Configurar Cron para Limpieza

```bash
# Agregar a crontab (cada 6 horas)
0 */6 * * * cd /path/to/backend && php artisan waitlist:clean
```

## 📡 Endpoints API

### Public Endpoints

#### POST `/api/waitlist`
Agregar cliente a lista de espera

**Request:**
```json
{
  "client_name": "Juan Pérez",
  "client_phone": "11 1234-5678",
  "client_email": "juan@email.com",
  "service_id": 1,
  "preferred_date": "2024-12-25",
  "preferred_time_start": "10:00",
  "preferred_time_end": "14:00",
  "barber_id": 1
}
```

**Response:**
```json
{
  "message": "¡Te agregamos a la lista de espera!",
  "data": { /* waitlist entry */ }
}
```

#### GET `/api/waitlist/{id}`
Ver detalles de entrada

#### DELETE `/api/waitlist/{id}`
Eliminar de lista (cancelar)

#### POST `/api/waitlist/{id}/confirm`
Confirmar turno desde notificación

**Request:**
```json
{
  "barber_id": 1,
  "starts_at": "2024-12-25 10:00:00",
  "ends_at": "2024-12-25 10:30:00"
}
```

### Admin Endpoints (Protegidos)

#### GET `/api/admin/waitlist`
Listar entradas de waitlist con filtros

**Query params:**
- `status`: WAITING | NOTIFIED | CONVERTED | EXPIRED
- `date`: YYYY-MM-DD
- `service_id`: number

#### GET `/api/admin/waitlist/stats`
Estadísticas de lista de espera

**Response:**
```json
{
  "data": {
    "waiting": 5,
    "notified": 2,
    "converted": 12,
    "expired": 3
  }
}
```

## 🎨 Frontend (Vue 3)

### Uso del Componente

```vue
<script setup>
import { ref } from 'vue'
import WaitlistModal from '@/components/WaitlistModal.vue'

const showWaitlistModal = ref(false)
const selectedService = ref({ id: 1, title: 'Corte' })
const selectedDate = ref('2024-12-25')
</script>

<template>
  <!-- Mostrar cuando no hay slots disponibles -->
  <div v-if="slots.length === 0">
    <p>No hay turnos disponibles</p>
    <button @click="showWaitlistModal = true">
      🔔 Unirme a Lista de Espera
    </button>
  </div>

  <WaitlistModal
    v-if="showWaitlistModal"
    :service="selectedService"
    :selected-date="selectedDate"
    @close="showWaitlistModal = false"
    @success="handleWaitlistSuccess"
  />
</template>
```

### Composable `useWaitlist`

```javascript
import { useWaitlist } from '@/composables/useWaitlist'

const { addToWaitlist, loading, error } = useWaitlist()

await addToWaitlist({
  client_name: 'Juan Pérez',
  client_phone: '11 1234-5678',
  // ...
})
```

## 📧 Email Template

El email enviado incluye:
- Saludo personalizado
- Detalles del turno disponible (fecha, hora, servicio, barbero)
- Botón "Confirmar Turno" con link único
- Recordatorio de ventana de 2 horas
- Diseño responsive compatible con Gmail, Outlook, etc.

## 🔐 Seguridad

- ✅ **Rate limiting**: Previene spam de solicitudes
- ✅ **Validación de entrada**: Todos los campos validados
- ✅ **Link único**: Tokens únicos para confirmación
- ✅ **Expiración automática**: Links expiran después de 2 horas
- ✅ **Protección CORS**: Solo frontend autorizado

## 🧪 Testing

### Test Manual

1. **Agregar a lista de espera:**
```bash
curl -X POST http://localhost:8000/api/waitlist \
  -H "Content-Type: application/json" \
  -d '{
    "client_name": "Test User",
    "client_phone": "123456789",
    "client_email": "test@email.com",
    "service_id": 1,
    "preferred_date": "2024-12-25"
  }'
```

2. **Simular cancelación:**
   - Crear turno en admin
   - Cancelar turno → Verifica que se envíe email

3. **Limpiar expirados:**
```bash
php artisan waitlist:clean
```

## 📊 Métricas de Éxito

- **Tasa de conversión**: % de notificaciones que terminan en turno confirmado
- **Tiempo de respuesta**: Tiempo promedio para confirmar
- **Turnos recuperados**: Cantidad de cancelaciones convertidas
- **Ocupación**: % de aumento en ocupación general

## 🚀 Mejoras Futuras

- [ ] **SMS notifications** (Twilio integration)
- [ ] **Push notifications** (PWA)
- [ ] **Priority levels** (clientes frecuentes tienen prioridad)
- [ ] **Multi-slot preferences** (ej: "cualquier día de la semana")
- [ ] **AI matching** (sugerir horarios alternativos similares)
- [ ] **Analytics dashboard** (métricas en admin panel)

## 🛠️ Troubleshooting

### Los emails no se envían

1. Verificar configuración SMTP en `.env`
2. Verificar que el queue worker esté corriendo:
   ```bash
   php artisan queue:work
   ```
3. Revisar logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Las notificaciones no se disparan

1. Verificar que `QUEUE_CONNECTION=database` en `.env`
2. Verificar que la tabla `jobs` existe:
   ```bash
   php artisan migrate
   ```
3. Verificar que el Job se está disparando:
   ```bash
   php artisan queue:failed
   ```

### Los clientes no reciben emails

- Revisar carpeta de spam
- Verificar que el email sea válido
- Revisar logs de Laravel para errores de SMTP

## 📞 Soporte

Para problemas o consultas, contactar al equipo de desarrollo.

---

**Versión:** 1.0.0  
**Última actualización:** Diciembre 2024
