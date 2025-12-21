import { ref } from 'vue'
import { validatePhone, validateName } from '@/utils/validators'

export function useBookingForm() {
  const formData = ref({
    service_id: '',
    barber_id: '',
    date: '',
    time: '',
    client_name: '',
    client_phone: '',
    client_email: '',
    notes: ''
  })

  const errors = ref({
    service_id: '',
    barber_id: '',
    date: '',
    time: '',
    client_name: '',
    client_phone: '',
    client_email: ''
  })

  const validateField = (field) => {
    errors.value[field] = ''

    switch (field) {
      case 'service_id':
        if (!formData.value.service_id) {
          errors.value.service_id = 'Seleccioná un servicio'
        }
        break
      
      case 'barber_id':
        if (!formData.value.barber_id) {
          errors.value.barber_id = 'Seleccioná un barbero'
        }
        break
      
      case 'date':
        if (!formData.value.date) {
          errors.value.date = 'Seleccioná una fecha'
        } else {
          const selectedDate = new Date(formData.value.date + 'T00:00:00')
          const today = new Date()
          today.setHours(0, 0, 0, 0)
          
          if (selectedDate < today) {
            errors.value.date = 'No podés seleccionar una fecha pasada'
            formData.value.date = ''
          } else if (selectedDate.getDay() === 0) {
            errors.value.date = 'No trabajamos los domingos. Por favor elegí otra fecha'
            formData.value.date = ''
          }
        }
        break
      
      case 'time':
        if (!formData.value.time) {
          errors.value.time = 'Seleccioná un horario'
        }
        break
      
      case 'client_name':
        if (!formData.value.client_name.trim()) {
          errors.value.client_name = 'Ingresá tu nombre completo'
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(formData.value.client_name)) {
          errors.value.client_name = 'El nombre solo puede contener letras'
        } else if (!validateName(formData.value.client_name)) {
          const words = formData.value.client_name.trim().split(/\s+/).filter(w => w.length > 0)
          if (words.length < 2) {
            errors.value.client_name = 'Ingresá nombre y apellido (ejemplo: Juan Pérez)'
          } else if (words.length > 2) {
            errors.value.client_name = 'Ingresá solo nombre y apellido'
          } else {
            errors.value.client_name = 'Cada nombre debe tener al menos 2 letras'
          }
        }
        break
      
      case 'client_phone':
        if (!formData.value.client_phone.trim()) {
          errors.value.client_phone = 'Ingresá tu teléfono'
        } else if (!validatePhone(formData.value.client_phone)) {
          errors.value.client_phone = 'Ingresá un teléfono válido (ej: 11 2345 6789)'
        }
        break
      
      case 'client_email':
        if (!formData.value.client_email.trim()) {
          errors.value.client_email = 'Ingresá tu email'
        } else if (!formData.value.client_email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
          errors.value.client_email = 'Ingresá un email válido'
        }
        break
    }
  }

  const validateForm = () => {
    validateField('service_id')
    validateField('barber_id')
    validateField('date')
    validateField('time')
    validateField('client_name')
    validateField('client_phone')
    validateField('client_email')

    return !Object.values(errors.value).some(error => error !== '')
  }

  const resetForm = () => {
    formData.value = {
      service_id: '',
      barber_id: '',
      date: '',
      time: '',
      client_name: '',
      client_phone: '',
      client_email: '',
      notes: ''
    }
    errors.value = {
      service_id: '',
      barber_id: '',
      date: '',
      time: '',
      client_name: '',
      client_phone: '',
      client_email: ''
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
