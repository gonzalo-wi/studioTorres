import { ref } from 'vue'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

const loading = ref(false)
const error = ref(null)

/**
 * Composable para manejar la lista de espera
 */
export function useWaitlist() {
  /**
   * Agregar cliente a lista de espera
   */
  async function addToWaitlist(data) {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_BASE_URL}/waitlist`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Error al unirse a lista de espera')
      }

      const result = await response.json()
      return result
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Obtener detalles de entrada de waitlist
   */
  async function getWaitlistEntry(id) {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_BASE_URL}/waitlist/${id}`)

      if (!response.ok) {
        throw new Error('No se pudo obtener la información')
      }

      const result = await response.json()
      return result.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Eliminar de lista de espera (cancelar)
   */
  async function removeFromWaitlist(id) {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_BASE_URL}/waitlist/${id}`, {
        method: 'DELETE',
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Error al eliminar de lista')
      }

      const result = await response.json()
      return result
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Confirmar turno desde notificación de waitlist
   */
  async function confirmFromWaitlist(id, slotData) {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_BASE_URL}/waitlist/${id}/confirm`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(slotData),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Error al confirmar turno')
      }

      const result = await response.json()
      return result
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    addToWaitlist,
    getWaitlistEntry,
    removeFromWaitlist,
    confirmFromWaitlist,
  }
}
