# 💈 Hernán Barber - Sistema de Reservas

Aplicación web completa para gestión de turnos de barbería, construida con Vue 3, Vue Router y Tailwind CSS.

## 🚀 Características

### Para Clientes
- ✂️ Visualización de servicios y precios
- 📅 Sistema de reserva de turnos online
- 🕐 Selección de horarios disponibles en tiempo real
- 📱 Diseño responsive y mobile-first
- 🎨 Galería de trabajos realizados

### Para Administradores
- 📊 Dashboard con estadísticas
- 📋 Gestión completa de turnos
- ✅ Confirmación y cancelación de reservas
- 🔍 Filtros avanzados por fecha, estado y servicio
- 🔐 Acceso protegido con autenticación

## 🛠️ Stack Tecnológico

- **Vue 3.5+** - Composition API con `<script setup>`
- **Vue Router 4.6+** - Navegación y guards
- **Tailwind CSS 3.4+** - Sistema de diseño
- **Vite 7+** - Build tool y dev server

## 📁 Estructura del Proyecto

```
Frontend/vue-barber/
├── src/
│   ├── components/          # Componentes reutilizables
│   │   ├── BaseButton.vue
│   │   ├── BaseInput.vue
│   │   ├── BaseSelect.vue
│   │   ├── BadgeStatus.vue
│   │   ├── Card.vue
│   │   ├── Modal.vue
│   │   ├── Navbar.vue
│   │   └── Footer.vue
│   │
│   ├── layouts/             # Layouts principales
│   │   ├── PublicLayout.vue
│   │   └── AdminLayout.vue
│   │
│   ├── pages/               # Páginas de la aplicación
│   │   ├── HomePage.vue
│   │   ├── ServicesPage.vue
│   │   ├── GalleryPage.vue
│   │   ├── BookPage.vue
│   │   ├── BookSuccessPage.vue
│   │   └── admin/
│   │       ├── LoginPage.vue
│   │       ├── DashboardPage.vue
│   │       ├── BookingsPage.vue
│   │       └── BookingDetailPage.vue
│   │
│   ├── composables/         # Lógica reutilizable
│   │   ├── useAuth.js
│   │   └── useBookingForm.js
│   │
│   ├── services/            # Servicios API
│   │   └── bookingsService.js
│   │
│   ├── utils/               # Utilidades
│   │   ├── dateHelpers.js
│   │   └── validators.js
│   │
│   ├── router/              # Configuración de rutas
│   │   └── index.js
│   │
│   ├── App.vue
│   ├── main.js
│   └── style.css
│
├── .env                     # Variables de entorno
├── tailwind.config.js       # Configuración Tailwind
├── vite.config.js           # Configuración Vite
└── package.json
```

## 🎨 Diseño

### Tema
- **Paleta**: Dark mode con tonos negros/grises
- **Acento**: Rojo (#DC2626) para CTAs principales
- **Alternativa**: Dorado para elementos premium
- **Tipografía**: Inter (texto) + Montserrat (títulos)

### Componentes Base
Todos los componentes siguen un sistema de diseño consistente:
- `BaseButton`: 4 variantes (primary, secondary, outline, danger)
- `BaseInput` / `BaseSelect`: Con validación integrada
- `BadgeStatus`: 4 estados (pending, confirmed, cancelled, rescheduled)
- `Card`: Con efectos hover opcionales
- `Modal`: Overlay con animaciones

## 🚦 Rutas

### Públicas
- `/` - Home
- `/services` - Servicios y precios
- `/gallery` - Catálogo de trabajos
- `/book` - Reservar turno (3 pasos)
- `/book/success` - Confirmación de reserva

### Admin (protegidas)
- `/admin/login` - Login
- `/admin` - Dashboard
- `/admin/bookings` - Lista de turnos
- `/admin/bookings/:id` - Detalle de turno

## ⚙️ Instalación y Uso

### Requisitos
- Node.js 18+ y npm

### Instalación
```bash
cd Frontend/vue-barber
npm install
```

### Desarrollo
```bash
npm run dev
```
Abre [http://localhost:5173](http://localhost:5173)

### Build para producción
```bash
npm run build
```

### Preview de producción
```bash
npm run preview
```

## 🔐 Autenticación Admin

Por defecto, la contraseña de admin es `admin123` (configurada en `.env`).

Para cambiarla, editá el archivo `.env`:
```bash
VITE_ADMIN_PASSWORD=tu_contraseña_segura
```

## 🧪 Datos Mock

El proyecto usa un servicio mock (`bookingsService.js`) que simula una API REST.
Cuando esté listo el backend, solo tenés que reemplazar las funciones mock por llamadas reales.

### Ejemplo de integración con API real:
```javascript
// services/bookingsService.js
const BASE_URL = import.meta.env.VITE_API_BASE_URL

export async function fetchBookings(filters = {}) {
  const params = new URLSearchParams(filters)
  const response = await fetch(`${BASE_URL}/bookings?${params}`)
  return response.json()
}
```

## 📝 Lógica de Reservas

### Servicios Disponibles
- **Corte**: 30 min - $3500
- **Corte + Barba**: 60 min - $5500
- **Barba**: 30 min - $2500
- **Afeitado**: 30 min - $3000

### Horarios
- Lunes a Viernes: 10:00 - 19:00
- Sábados: 10:00 - 17:00
- Domingos: Cerrado

### Slots
Los horarios se generan cada 30 minutos. Para servicios de 60 min, se bloquean 2 slots consecutivos.

## 🎯 Validaciones

### Formulario de Reserva
- ✅ Servicio: Obligatorio
- ✅ Fecha: Obligatoria, no domingos, máximo 30 días adelante
- ✅ Hora: Obligatoria, debe estar disponible
- ✅ Nombre: Mínimo 2 caracteres
- ✅ Teléfono: Formato argentino (validado con regex)
- ⭕ Observaciones: Opcional

## 🚀 Próximas Mejoras

- [ ] Integración con backend real
- [ ] Autenticación JWT
- [ ] Notificaciones por email/SMS
- [ ] Sistema de recordatorios automáticos
- [ ] Estadísticas y reportes avanzados
- [ ] Gestión de múltiples barberos
- [ ] Sistema de pagos online

## 🤝 Contribuir

1. Fork el proyecto
2. Creá una rama: `git checkout -b feature/nueva-funcionalidad`
3. Commit: `git commit -m 'Agregar nueva funcionalidad'`
4. Push: `git push origin feature/nueva-funcionalidad`
5. Abrí un Pull Request

## 📄 Licencia

Proyecto privado - Todos los derechos reservados © 2025

---

Desarrollado con ❤️ para Hernán Barber
