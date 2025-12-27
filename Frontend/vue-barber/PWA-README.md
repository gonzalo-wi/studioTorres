# PWA - Progressive Web App

## ✅ Implementación Completada

La aplicación ahora es una **Progressive Web App (PWA)** que puede instalarse como aplicación nativa en dispositivos móviles y de escritorio.

## 🎯 Características Implementadas

### 1. **Instalación como App**
- Los usuarios verán un banner de instalación después de 3 segundos
- Pueden instalar la app en su dispositivo
- Icono en la pantalla de inicio
- Experiencia de pantalla completa (sin barra del navegador)

### 2. **Funcionamiento Offline**
- Las páginas visitadas se guardan en caché
- Puede funcionar sin conexión a internet (páginas ya vistas)
- Las imágenes se cachean por 30 días
- Las llamadas API se cachean por 5 minutos

### 3. **Optimizaciones**
- Service Worker registrado automáticamente
- Actualización automática cuando hay nueva versión
- Caché inteligente de recursos estáticos

## 📱 Cómo Instalar

### **En Android (Chrome/Edge)**
1. Visita la web
2. Espera el banner "Instalar Aplicación"
3. Toca "Instalar"
4. O ve a Menú (⋮) → "Instalar app" o "Agregar a pantalla de inicio"

### **En iOS (Safari)**
1. Visita la web
2. Toca el botón "Compartir" (cuadrado con flecha)
3. Selecciona "Agregar a pantalla de inicio"
4. Toca "Agregar"

### **En PC (Chrome/Edge)**
1. Visita la web
2. Clic en el icono ➕ en la barra de direcciones
3. O ve a Menú (⋮) → "Instalar Studio Torres"

## 🔧 Archivos Creados/Modificados

### Nuevos Archivos:
- `vite.config.js` - Configuración PWA con VitePWA
- `src/components/InstallPWA.vue` - Banner de instalación
- `public/pwa-192x192.png` - Icono 192x192
- `public/pwa-512x512.png` - Icono 512x512
- `generate-icons.ps1` - Script para generar iconos

### Archivos Modificados:
- `index.html` - Meta tags PWA y Apple
- `src/main.js` - Registro del Service Worker
- `src/App.vue` - Componente InstallPWA incluido

## 🚀 Características de la PWA

### Manifest (manifest.json)
```json
{
  "name": "Barbería Hernán Torres",
  "short_name": "Torres Barber",
  "theme_color": "#d4af37",
  "background_color": "#ffffff",
  "display": "standalone"
}
```

### Estrategia de Caché

**Imágenes (CacheFirst)**:
- Se guardan por 30 días
- Máximo 50 imágenes
- Carga instantánea desde caché

**API (NetworkFirst)**:
- Intenta red primero
- Caché como respaldo
- Timeout de 10 segundos
- Se guarda por 5 minutos

## 🧪 Probar en Desarrollo

El plugin está configurado con `devOptions.enabled = true`, por lo que puedes probar la PWA en desarrollo:

```bash
npm run dev
```

Abre DevTools → Application → Service Workers para ver el estado.

## 📦 Build para Producción

```bash
npm run build
```

Esto generará:
- `dist/manifest.webmanifest` - Configuración PWA
- `dist/sw.js` - Service Worker
- `dist/workbox-*.js` - Librerías de caché

## 🔔 Próximas Mejoras (Opcionales)

### Notificaciones Push
Requiere:
- Backend que maneje suscripciones push
- VAPID keys
- Service Worker modificado para recibir notificaciones

### Background Sync
- Sincronizar turnos cuando vuelve la conexión
- Útil si reservan offline

### Share API
- Compartir turnos con amigos
- Compartir links a la barbería

## 🐛 Troubleshooting

### "No se muestra el banner de instalación"
- Verifica que estés en HTTPS (CloudFlare tunnel)
- Abre en navegador incógnito
- Revisa que no esté ya instalada
- Borra localStorage: `pwa-install-dismissed`

### "Service Worker no se registra"
- Abre DevTools → Console
- Verifica errores de Service Worker
- Asegúrate que `npm run build` se ejecutó correctamente

### "Iconos no se ven"
- Verifica que existan en `public/`
- Regenera con: `./generate-icons.ps1`
- Verifica tamaños: 192x192 y 512x512

## 📊 Métricas

Después de implementar, monitorea:
- % de usuarios que instalan
- Tasa de retorno (¿vuelven más seguido?)
- Uso offline
- Velocidad de carga

## 🎨 Personalización

### Cambiar Color del Tema
Edita en `vite.config.js`:
```javascript
theme_color: '#d4af37', // Color dorado actual
```

### Cambiar Nombre de la App
Edita en `vite.config.js`:
```javascript
name: 'Barbería Hernán Torres',
short_name: 'Torres Barber',
```

### Splash Screen (iOS)
Agregar en `public/` splash screens para diferentes tamaños de iPhone/iPad.

---

## ✅ Estado: IMPLEMENTADO Y FUNCIONANDO

La PWA está lista para usarse. Al hacer deploy en producción, los usuarios podrán instalarla inmediatamente.
