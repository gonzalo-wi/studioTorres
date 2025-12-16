/**
 * Valida un número de teléfono argentino
 * @param {string} phone
 * @returns {boolean}
 */
export function validatePhone(phone) {
  // Acepta formatos: 1234567890, 11-2345-6789, +54 9 11 2345 6789, etc.
  const phoneRegex = /^(\+?54)?[\s\-]?9?[\s\-]?(\d{2,4})[\s\-]?(\d{4})[\s\-]?(\d{4})$/
  return phoneRegex.test(phone.replace(/\s/g, ''))
}

/**
 * Valida un nombre
 * @param {string} name
 * @returns {boolean}
 */
export function validateName(name) {
  return name.trim().length >= 2
}

/**
 * Valida un email
 * @param {string} email
 * @returns {boolean}
 */
export function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Formatea un número de teléfono para display
 * @param {string} phone
 * @returns {string}
 */
export function formatPhoneDisplay(phone) {
  const cleaned = phone.replace(/\D/g, '')
  
  if (cleaned.length === 10) {
    return `${cleaned.slice(0, 2)} ${cleaned.slice(2, 6)}-${cleaned.slice(6)}`
  }
  
  return phone
}
