/**
 * Genera slots de horarios disponibles
 * @param {string} date - Fecha en formato YYYY-MM-DD
 * @param {number} serviceDuration - Duración del servicio en minutos
 * @param {Array} existingBookings - Reservas existentes para esa fecha
 * @returns {Array} - Array de horarios disponibles
 */
export function generateTimeSlots(date, serviceDuration, existingBookings = []) {
  const slots = []
  const start = 10 // 10:00
  const end = 19   // 19:00
  
  for (let hour = start; hour < end; hour++) {
    for (let min of [0, 30]) {
      const time = `${hour.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`
      
      // Verificar si el slot actual está disponible
      const isCurrentSlotAvailable = !existingBookings.some(
        b => b.time === time && b.date === date && b.status !== 'CANCELLED'
      )
      
      // Si el servicio dura 60 min, verificar también el siguiente slot
      if (serviceDuration === 60) {
        const nextSlot = addMinutes(time, 30)
        
        // No considerar slots que se pasen del horario de cierre
        if (hour === end - 1 && min === 30) continue
        
        const isNextSlotAvailable = !existingBookings.some(
          b => b.time === nextSlot && b.date === date && b.status !== 'CANCELLED'
        )
        
        if (isCurrentSlotAvailable && isNextSlotAvailable) {
          slots.push({ time, available: true })
        }
      } else {
        if (isCurrentSlotAvailable) {
          slots.push({ time, available: true })
        }
      }
    }
  }
  
  return slots
}

/**
 * Suma minutos a una hora
 * @param {string} time - Hora en formato HH:MM
 * @param {number} minutes - Minutos a sumar
 * @returns {string} - Nueva hora en formato HH:MM
 */
export function addMinutes(time, minutes) {
  const [hours, mins] = time.split(':').map(Number)
  const totalMinutes = hours * 60 + mins + minutes
  const newHours = Math.floor(totalMinutes / 60)
  const newMins = totalMinutes % 60
  return `${newHours.toString().padStart(2, '0')}:${newMins.toString().padStart(2, '0')}`
}

/**
 * Formatea una fecha
 * @param {string} date - Fecha en formato YYYY-MM-DD
 * @returns {string} - Fecha formateada (ej: "Lunes 15 de Diciembre")
 */
export function formatDate(date) {
  const dateObj = new Date(date + 'T00:00:00')
  const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
  const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
  
  const dayName = days[dateObj.getDay()]
  const day = dateObj.getDate()
  const month = months[dateObj.getMonth()]
  
  return `${dayName} ${day} de ${month}`
}

/**
 * Obtiene la fecha de hoy en formato YYYY-MM-DD
 * @returns {string}
 */
export function getTodayDate() {
  const today = new Date()
  return today.toISOString().split('T')[0]
}

/**
 * Obtiene la fecha máxima para reservas (7 días desde hoy)
 * @returns {string}
 */
export function getMaxDate() {
  const today = new Date()
  const maxDate = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000)
  return maxDate.toISOString().split('T')[0]
}

/**
 * Verifica si una fecha es válida para reservas
 * @param {string} date - Fecha en formato YYYY-MM-DD
 * @returns {boolean}
 */
export function isValidBookingDate(date) {
  const dateObj = new Date(date + 'T00:00:00')
  const day = dateObj.getDay()
  
  // No permitir domingos (0)
  if (day === 0) return false
  
  const today = getTodayDate()
  const max = getMaxDate()
  
  return date >= today && date <= max
}
