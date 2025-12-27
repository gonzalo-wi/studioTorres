<template>
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="translate-y-full opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-full opacity-0"
  >
    <div v-if="showInstallPrompt || showIOSPrompt" class="fixed bottom-0 left-0 right-0 z-50">
      <!-- Banner para Chrome/Edge Android -->
      <div
        v-if="showInstallPrompt && !isIOS"
        class="p-4 bg-gradient-to-r from-gold-500 to-gold-600 shadow-2xl border-t-2 border-gold-400"
      >
      <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-3">
          <div class="flex-shrink-0 bg-white rounded-full p-2">
            <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
          
          <div class="flex-1 text-white min-w-0">
            <h3 class="font-bold text-base mb-1">Instalar App</h3>
            <p class="text-xs text-white/90">Acceso rápido desde tu pantalla de inicio</p>
          </div>
          
          <div class="flex gap-2 flex-shrink-0">
            <button
              @click="dismissPrompt"
              class="px-3 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-semibold transition-colors"
            >
              No
            </button>
            <button
              @click="installApp"
              class="px-4 py-2 bg-white text-gold-600 hover:bg-gold-50 rounded-lg text-sm font-bold transition-colors shadow-lg"
            >
              Instalar
            </button>
          </div>
        </div>
      </div>
      </div>

      <!-- Banner para iOS Safari -->
      <div
        v-if="showIOSPrompt"
        class="p-4 bg-gradient-to-r from-gold-500 to-gold-600 shadow-2xl border-t-2 border-gold-400"
      >
      <div class="max-w-4xl mx-auto">
        <button
          @click="dismissPrompt"
          class="absolute top-2 right-2 text-white/80 hover:text-white"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="text-white pr-8">
          <h3 class="font-bold text-base mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Instalar en iPhone
          </h3>
          
          <ol class="text-sm space-y-2 text-white/95">
            <li class="flex items-start gap-2">
              <span class="font-bold">1.</span>
              <span>Toca el botón <span class="inline-flex items-center mx-1 px-1.5 py-0.5 bg-white/20 rounded">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M16 5l-1.42 1.42-1.59-1.59V16h-1.98V4.83L9.42 6.42 8 5l4-4 4 4zm4 5v11c0 1.1-.9 2-2 2H6c-1.11 0-2-.9-2-2V10c0-1.11.89-2 2-2h3v2H6v11h12V10h-3V8h3c1.1 0 2 .89 2 2z"/>
                </svg>
              </span> (Compartir) en la barra inferior</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="font-bold">2.</span>
              <span>Desliza y selecciona "Agregar a pantalla de inicio"</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="font-bold">3.</span>
              <span>Toca "Agregar" en la esquina superior derecha</span>
            </li>
          </ol>
        </div>
      </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const showInstallPrompt = ref(false)
const showIOSPrompt = ref(false)
const deferredPrompt = ref(null)
const isIOS = ref(false)

onMounted(() => {
  // Detectar iOS
  const ua = window.navigator.userAgent
  const iOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream
  isIOS.value = iOS

  // Detectar si ya está instalada
  const isStandalone = window.matchMedia('(display-mode: standalone)').matches
  const isIOSStandalone = 'standalone' in window.navigator && window.navigator.standalone
  
  if (isStandalone || isIOSStandalone) {
    return
  }

  // Verificar si el usuario ya rechazó la instalación
  const dismissed = localStorage.getItem('pwa-install-dismissed')
  const dismissedTime = dismissed ? parseInt(dismissed) : 0
  const now = Date.now()
  
  // Solo mostrar si pasaron 7 días desde que rechazó
  const shouldShow = !dismissed || (now - dismissedTime) > 7 * 24 * 60 * 60 * 1000

  if (!shouldShow) {
    return
  }

  // iOS Safari - mostrar instrucciones manuales
  if (iOS) {
    setTimeout(() => {
      showIOSPrompt.value = true
    }, 3000)
  } else {
    // Chrome/Edge Android - usar beforeinstallprompt
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault()
      deferredPrompt.value = e
      
      setTimeout(() => {
        showInstallPrompt.value = true
      }, 3000)
    })

    // Escuchar cuando se instala
    window.addEventListener('appinstalled', () => {
      showInstallPrompt.value = false
      deferredPrompt.value = null
      localStorage.removeItem('pwa-install-dismissed')
    })
  }
})

async function installApp() {
  if (!deferredPrompt.value) return

  deferredPrompt.value.prompt()
  
  const { outcome } = await deferredPrompt.value.userChoice
  
  if (outcome === 'accepted') {
    console.log('PWA instalada')
  }
  
  deferredPrompt.value = null
  showInstallPrompt.value = false
}

function dismissPrompt() {
  showInstallPrompt.value = false
  showIOSPrompt.value = false
  localStorage.setItem('pwa-install-dismissed', Date.now().toString())
}
</script>
