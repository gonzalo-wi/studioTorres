import { ref } from 'vue'
import { fetchBarbers, fetchBarber, createBarber, updateBarber, deleteBarber, updateSchedules, fetchBarberAppointments, fetchBarberEarnings } from '@/services/barbersService'

export function useBarbers() {
  const barbers = ref([])
  const selectedBarber = ref(null)
  const loading = ref(false)
  const error = ref(null)

  async function loadBarbers() {
    loading.value = true
    error.value = null
    try {
      const res = await fetchBarbers()
      barbers.value = Array.isArray(res?.data) ? res.data : res
    } catch (e) {
      error.value = e?.message || 'Error cargando barberos'
    } finally {
      loading.value = false
    }
  }

  async function loadBarber(id) {
    loading.value = true
    error.value = null
    try {
      const res = await fetchBarber(id)
      selectedBarber.value = res?.data || res
    } catch (e) {
      error.value = e?.message || 'Error cargando barbero'
    } finally {
      loading.value = false
    }
  }

  async function saveBarber(payload) {
    return createBarber(payload)
  }

  async function editBarber(id, payload) {
    return updateBarber(id, payload)
  }

  async function removeBarber(id) {
    return deleteBarber(id)
  }

  async function saveSchedules(id, schedules) {
    return updateSchedules(id, schedules)
  }

  async function loadAppointments(id, params) {
    return fetchBarberAppointments(id, params)
  }

  async function loadEarnings(id, params) {
    return fetchBarberEarnings(id, params)
  }

  return { barbers, selectedBarber, loading, error, loadBarbers, loadBarber, saveBarber, editBarber, removeBarber, saveSchedules, loadAppointments, loadEarnings }
}
