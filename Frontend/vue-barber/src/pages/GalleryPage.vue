<script setup>
import { ref } from 'vue'
import Card from '@/components/Card.vue'
import Modal from '@/components/Modal.vue'

const selectedImage = ref(null)
const showModal = ref(false)

const categories = [
  { id: 'all', label: 'Todos' },
  { id: 'cortes', label: 'Cortes' },
  { id: 'barbas', label: 'Barbas' },
  { id: 'fades', label: 'Fades' },
  { id: 'clasicos', label: 'Clásicos' }
]

const selectedCategory = ref('all')

// Galería de trabajos con imágenes reales
const gallery = [
  { id: 1, src: '/src/assets/cortes/corte1.jpeg', alt: 'Corte profesional 1', category: 'cortes' },
  { id: 2, src: '/src/assets/cortes/corte2.jpeg', alt: 'Corte profesional 2', category: 'cortes' },
  { id: 3, src: '/src/assets/cortes/corte3.jpeg', alt: 'Corte profesional 3', category: 'cortes' },
  { id: 4, src: '/src/assets/cortes/corte4.jpeg', alt: 'Corte profesional 4', category: 'cortes' }
]

const filteredGallery = computed(() => {
  if (selectedCategory.value === 'all') {
    return gallery
  }
  return gallery.filter(item => item.category === selectedCategory.value)
})

const openImage = (image) => {
  selectedImage.value = image
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedImage.value = null
}
</script>

<script>
import { computed } from 'vue'
</script>

<template>
  <div class="py-12">
    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-5xl font-display font-bold text-white mb-4">
          Catálogo de Trabajos
        </h1>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">
          Explorá nuestros trabajos más recientes. 
          Cada corte es único y personalizado para cada cliente.
        </p>
      </div>

      <!-- Category Filter -->
      <div class="flex flex-wrap justify-center gap-3 mb-12">
        <button
          v-for="category in categories"
          :key="category.id"
          @click="selectedCategory = category.id"
          :class="[
            'px-6 py-3 rounded-lg font-semibold transition-all duration-200',
            selectedCategory === category.id
              ? 'bg-primary-600 text-white'
              : 'bg-dark-800 text-gray-300 hover:bg-dark-700 hover:text-white'
          ]"
        >
          {{ category.label }}
        </button>
      </div>

      <!-- Gallery Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <Card
          v-for="image in filteredGallery"
          :key="image.id"
          hover
          clickable
          @click="openImage(image)"
          class="group overflow-hidden"
        >
          <div class="aspect-square bg-dark-800 relative overflow-hidden">
            <!-- Imagen real -->
            <img 
              :src="image.src" 
              :alt="image.alt"
              class="absolute inset-0 w-full h-full object-cover"
            />
            
            <!-- Hover overlay -->
            <div class="absolute inset-0 bg-primary-600/0 group-hover:bg-primary-600/20 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
              </svg>
            </div>
          </div>
          
          <div class="p-4">
            <h3 class="text-white font-semibold">{{ image.alt }}</h3>
          </div>
        </Card>
      </div>

      <!-- Empty state -->
      <div v-if="filteredGallery.length === 0" class="text-center py-20">
        <p class="text-gray-400 text-lg">
          No hay imágenes en esta categoría todavía.
        </p>
      </div>
    </div>

    <!-- Image Modal -->
    <Modal
      :show="showModal"
      @close="closeModal"
      size="lg"
    >
      <div v-if="selectedImage" class="text-center">
        <div class="rounded-lg mb-4 overflow-hidden">
          <img 
            :src="selectedImage.src" 
            :alt="selectedImage.alt"
            class="w-full h-auto object-contain max-h-[70vh]"
          />
        </div>
        <h3 class="text-2xl font-display font-bold text-white">
          {{ selectedImage.alt }}
        </h3>
      </div>
    </Modal>
  </div>
</template>
