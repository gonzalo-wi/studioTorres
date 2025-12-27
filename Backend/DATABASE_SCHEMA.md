# Esquema de Base de Datos - HernanBarber

## Estructura Implementada

### 1. **users**
```sql
- id (PK)
- name
- email (UNIQUE)
- phone
- password
- role (ENUM: 'ADMIN', 'BARBER', 'CLIENT')
- created_at
- updated_at
```

### 2. **barbers**
Perfil de barbero asociado a un usuario.
```sql
- id (PK)
- user_id (FK UNIQUE -> users.id)
- active (boolean)
- notes (text, nullable)
- created_at
- updated_at
```

### 3. **services**
Servicios ofrecidos por la barbería.
```sql
- id (PK)
- title
- description (text, nullable)
- duration_minutes (int)
- price (decimal)
- active (boolean)
- created_at
- updated_at
```

### 4. **appointments**
Turnos/citas reservadas.
```sql
- id (PK)
- public_code (string, UNIQUE)
- client_name
- client_phone
- client_email (nullable)
- barber_id (FK -> barbers.id)
- service_id (FK -> services.id)
- starts_at (datetime)
- ends_at (datetime)
- notes (text, nullable)
- status (ENUM: 'PENDING', 'CONFIRMED', 'CANCELLED', 'DONE', 'NO_SHOW')
- created_at
- updated_at

UNIQUE (barber_id, starts_at) -- Previene doble reserva
INDEX (barber_id, starts_at)
INDEX (status)
```

### 5. **barber_schedules**
Horarios de disponibilidad semanal de cada barbero.
```sql
- id (PK)
- barber_id (FK -> barbers.id)
- weekday (tinyint: 0-6, 0=Domingo, 6=Sábado)
- start_time (time)
- end_time (time)
- created_at
- updated_at

INDEX (barber_id, weekday)
```

### 6. **barber_time_off**
Excepciones/ausencias de barberos.
```sql
- id (PK)
- barber_id (FK -> barbers.id)
- starts_at (datetime)
- ends_at (datetime)
- reason (string, nullable)
- created_at
- updated_at

INDEX (barber_id, starts_at, ends_at)
```

### 7. **waitlist**
Lista de espera para notificación cuando se liberan turnos.
```sql
- id (PK)
- client_name
- client_phone
- client_email (nullable)
- service_id (FK -> services.id)
- preferred_date (date)
- preferred_time_start (time, nullable)
- preferred_time_end (time, nullable)
- barber_id (FK -> barbers.id, nullable)
- status (ENUM: 'WAITING', 'NOTIFIED', 'CONVERTED', 'EXPIRED')
- notified_at (datetime, nullable)
- expires_at (datetime, nullable)
- created_at
- updated_at

INDEX (service_id, preferred_date, status)
INDEX (status, expires_at)
```

## Relaciones

- `users` 1:1 `barbers` (un user puede ser barbero)
- `barbers` 1:N `appointments` (un barbero tiene muchos turnos)
- `services` 1:N `appointments` (un servicio se usa en muchos turnos)
- `barbers` 1:N `barber_schedules` (un barbero tiene múltiples horarios semanales)
- `barbers` 1:N `barber_time_off` (un barbero puede tener múltiples ausencias)
- `services` 1:N `waitlist` (un servicio puede tener múltiples entradas en lista de espera)
- `barbers` 1:N `waitlist` (un barbero puede ser preferido por múltiples clientes en espera)

## Mejoras Implementadas

✅ **users**: Agregado campo `phone` y rol tipo ENUM con valores específicos  
✅ **barbers**: Tabla separada referenciando `user_id` (un barbero ES un usuario)  
✅ **services**: Campo `title` en lugar de `name`, sin horarios (estos van en appointments)  
✅ **appointments**: 
   - Usa `starts_at` y `ends_at` (datetime) en lugar de date+time separados
   - Referencia a `barber_id` 
   - Agrega `client_email` y renombra campos client_*
   - Status incluye 'NO_SHOW'
   - UNIQUE constraint en (barber_id, starts_at) para prevenir doble booking
✅ **barber_schedules**: Modela horarios disponibles por día de semana  
✅ **barber_time_off**: Modela excepciones/ausencias con rango datetime
✅ **waitlist**: Sistema de lista de espera inteligente con notificaciones automáticas

## Nuevas Funcionalidades

### 🔔 Sistema de Lista de Espera (Waitlist)
- Notificación automática cuando se cancela un turno
- Preferencias flexibles de horario
- Ventana de confirmación de 2 horas
- Prioridad FIFO (First In, First Out)
- Auto-expiración después de 7 días
- Ver [WAITLIST_README.md](../../WAITLIST_README.md) para más detalles

## Validaciones Recomendadas

1. **Doble booking**: El constraint UNIQUE en `appointments(barber_id, starts_at)` previene turnos simultáneos
2. **Solapamiento**: Validar en lógica de negocio que `ends_at` no solape con otro turno del mismo barbero
3. **Disponibilidad**: Verificar contra `barber_schedules` y `barber_time_off` antes de confirmar turno
