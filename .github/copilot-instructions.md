# HernanBarber - AI Coding Instructions

## Project Overview

Barbershop booking application with Vue 3 frontend and separate backend. Frontend is a modern, masculine-themed SPA for client bookings and admin management.

## Architecture

- **Monorepo structure**: `Frontend/vue-barber/` (Vue 3 + Vite) and `Backend/` (empty - to be implemented)
- **Frontend stack**: Vue 3.5+ with Composition API (`<script setup>`), Vue Router 4.6+, Tailwind CSS 3.4+, Vite 7+
- **No state management library**: Use composables pattern (`useAuth`, `useBookings`) instead of Pinia/Vuex

## Project Structure (to be built)

```
Frontend/vue-barber/src/
├── pages/           # Route views (Home, Services, Gallery, Book, Admin views)
├── components/      # Reusable UI (BaseButton, BaseInput, Card, Modal, BadgeStatus)
├── layouts/         # PublicLayout, AdminLayout (with navigation)
├── services/        # API layer (apiClient.js, bookingsService.js, catalogService.js)
├── composables/     # useAuth.js, useBookings.js, useSlots.js
├── types/           # TypeScript-style JSDoc type definitions
├── utils/           # Date/time helpers, slot generators
└── router/          # Vue Router config with route guards
```

## Development Commands

```bash
cd Frontend/vue-barber
npm run dev      # Start dev server (Vite)
npm run build    # Production build
npm run preview  # Preview production build
```

## Coding Conventions

### Vue Components

- **Always use Composition API** with `<script setup>` syntax
- **Component naming**: PascalCase for files (`BaseButton.vue`), kebab-case in templates (`<base-button>`)
- **Props**: Define with `defineProps()` with validation
- **Emits**: Declare with `defineEmits()`
- **Example pattern**:

```vue
<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  label: { type: String, required: true },
  variant: { type: String, default: 'primary' }
})

const emit = defineEmits(['click'])

const isActive = ref(false)
const buttonClasses = computed(() => ({
  'bg-red-600': props.variant === 'primary',
  'bg-gray-700': props.variant === 'secondary'
}))
</script>
```

### Services & API

- **Base URL**: Use `import.meta.env.VITE_API_BASE_URL` from `.env` files
- **API client**: Centralized `services/apiClient.js` with fetch wrapper
- **Mock data**: Use mock services initially (`services/mockBookingsService.js`) until backend is ready
- **Pattern**:

```javascript
// services/apiClient.js
const BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:3000/api'

export async function fetchAPI(endpoint, options = {}) {
  const response = await fetch(`${BASE_URL}${endpoint}`, {
    headers: { 'Content-Type': 'application/json', ...options.headers },
    ...options
  })
  if (!response.ok) throw new Error(`API Error: ${response.status}`)
  return response.json()
}
```

### Styling & Design System

- **Theme**: Dark, masculine palette - blacks/grays with red (#DC2626) or gold accents
- **Tailwind config**: Customize in `tailwind.config.js` with brand colors
- **Typography**: Strong, bold fonts (consider Inter, Montserrat weights 600-800)
- **Base components**: Create consistent design tokens
  - Buttons: `bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6`
  - Inputs: `bg-gray-800 border-gray-700 text-white focus:border-red-500`
  - Cards: `bg-gray-900 border border-gray-800`

### Routing

- **Public routes**: `/`, `/services`, `/gallery`, `/book`, `/book/success`
- **Admin routes**: `/admin/login`, `/admin`, `/admin/bookings`, `/admin/bookings/:id`
- **Auth guard**: Protect `/admin/*` routes with fake auth (localStorage token check)
- **Layouts**: Use layout components in routes (`<PublicLayout>` wraps public pages, `<AdminLayout>` wraps admin)

## Business Logic

### Booking Flow

1. **Service selection**: "Corte" (30 min) or "Corte + Barba" (60 min)
2. **Date picker**: Show available dates
3. **Time slots**: Generate 30-min intervals (10:00-19:00), block consecutive slots for 60-min services
4. **Form**: Name (required), Phone (required, validate format), Observations (optional)
5. **Validation**: All required fields, valid phone, slot availability
6. **States**: `PENDING`, `CONFIRMED`, `CANCELLED`, `RESCHEDULED`

### Slot Generation Logic

```javascript
// utils/slots.js
export function generateTimeSlots(date, serviceDuration, existingBookings = []) {
  const slots = []
  const start = 10 // 10:00
  const end = 19   // 19:00
  
  for (let hour = start; hour < end; hour++) {
    for (let min of [0, 30]) {
      const time = `${hour.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`
      const isAvailable = !existingBookings.some(b => b.time === time && b.date === date)
      
      // If service is 60 min, also check next slot
      if (serviceDuration === 60) {
        const nextSlot = addMinutes(time, 30)
        const nextAvailable = !existingBookings.some(b => b.time === nextSlot && b.date === date)
        if (isAvailable && nextAvailable) slots.push(time)
      } else {
        if (isAvailable) slots.push(time)
      }
    }
  }
  return slots
}
```

### Admin Features

- **Dashboard**: Show KPIs (today's bookings, pending, confirmed, cancelled counts)
- **Bookings table**: Filterable by date, status, service with action buttons
- **Actions**: Confirm, Cancel, Reschedule (update status + optional date/time)

## State Management Pattern

Use composables instead of Pinia:

```javascript
// composables/useBookings.js
import { ref } from 'vue'
import { fetchBookings, createBooking } from '@/services/bookingsService'

const bookings = ref([])
const loading = ref(false)

export function useBookings() {
  async function loadBookings(filters = {}) {
    loading.value = true
    try {
      bookings.value = await fetchBookings(filters)
    } finally {
      loading.value = false
    }
  }
  
  return { bookings, loading, loadBookings }
}
```

## Key Files to Reference

- [main.js](Frontend/vue-barber/src/main.js) - App entry point, router + app initialization
- [package.json](Frontend/vue-barber/package.json) - Dependencies (Vue 3.5+, Vue Router 4.6+, Tailwind 3.4+)
- [vite.config.js](Frontend/vue-barber/vite.config.js) - Build configuration

## Testing & Validation

- **Form validation**: Use composable pattern (`useFormValidation`) with reactive errors
- **Phone validation**: Regex pattern for Argentinian phones (optional country code)
- **Loading states**: Always show loading indicators during async operations
- **Error states**: Display user-friendly messages, never crash on API errors
- **Empty states**: Show helpful messages when no data exists

## Environment Variables

Create `.env` and `.env.production`:

```bash
VITE_API_BASE_URL=http://localhost:3000/api
VITE_ADMIN_PASSWORD=admin123  # Fake auth password
```

## Mobile-First Responsive Design

- Design for mobile first (375px), then tablet (768px), desktop (1024px+)
- Navbar: Hamburger menu on mobile, full nav on desktop
- Booking form: Single column on mobile, two columns on desktop
- Admin table: Horizontal scroll on mobile, full table on desktop
