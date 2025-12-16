import { ref } from 'vue'
import { validatePhone, validateName } from '@/utils/validators'

export function useBookingForm() {
  const formData = ref({
    service: '',
    date: '',
    time: '',
    name: '',
    phone: '',
    observations: ''
  })

  const errors = ref({
    service: '',
    date: '',
    time: '',
    name: '',
    phone: ''
  })

  const validateField = (field) => {
    errors.value[field] = ''

    switch (field) {
      case 'service':
        if (!formData.value.service) {
          errors.value.service = 'Seleccioná un servicio'
        }
        break
      
      case 'date':
        if (!formData.value.date) {
          errors.value.date = 'Seleccioná una fecha'
        }
        break
      
      case 'time':
        if (!formData.value.time) {
          errors.value.time = 'Seleccioná un horario'
        }
        break
      
      case 'name':
        if (!formData.value.name.trim()) {
          errors.value.name = 'Ingresá tu nombre'
        } else if (!validateName(formData.value.name)) {
          errors.value.name = 'El nombre debe tener al menos 2 caracteres'
        }
        break
      
      case 'phone':
        if (!formData.value.phone.trim()) {
          errors.value.phone = 'Ingresá tu teléfono'
        } else if (!validatePhone(formData.value.phone)) {
          errors.value.phone = 'Ingresá un teléfono válido (ej: 11 2345 6789)'
        }
        break
    }
  }

  const validateForm = () => {
    validateField('service')
    validateField('date')
    validateField('time')
    validateField('name')
    validateField('phone')

    return !Object.values(errors.value).some(error => error !== '')
  }

  const resetForm = () => {
    formData.value = {
      service: '',
      date: '',
      time: '',
      name: '',
      phone: '',
      observations: ''
    }
    errors.value = {
      service: '',
      date: '',
      time: '',
      name: '',
      phone: ''
    }
  }

  return {
    formData,
    errors,
    validateField,
    validateForm,
    resetForm
  }
}
