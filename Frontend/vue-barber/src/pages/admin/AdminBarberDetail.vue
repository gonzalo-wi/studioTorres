<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <router-link to="/admin/barbers" class="text-dark-600 hover:text-dark-800 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </router-link>
          <div>
            <h1 class="text-3xl font-bold text-dark-800">{{ barber?.name || 'Barbero' }}</h1>
            <p class="text-dark-600 mt-1">{{ barber?.user?.email }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto space-y-8">
      <!-- Foto de Perfil Section -->
      <div class="bg-white border border-gold-200 rounded-xl overflow-hidden shadow-lg">
        <div class="bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-4">
          <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h2 class="text-xl font-bold text-white">Foto de Perfil</h2>
          </div>
          <p class="text-gold-100 text-sm mt-1">La foto se mostrará al momento de reservar turno</p>
        </div>
        
        <div class="p-6">
          <div class="flex items-start gap-6">
            <div class="flex-shrink-0">
              <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 border-4 border-gold-300">
                <img v-if="previewUrl || barber?.avatar_full_url" 
                  :src="previewUrl || barber.avatar_full_url" 
                  :alt="barber.name" 
                  class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-dark-400">
                  <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
              </div>
              <p v-if="previewUrl" class="text-center text-sm text-gold-600 font-semibold mt-2">Vista previa</p>
            </div>
            
            <div class="flex-1">
              <form @submit.prevent="uploadAvatar" class="space-y-4">
                <div>
                  <label class="block text-dark-700 font-semibold mb-2">Subir nueva foto</label>
                  <input @change="handleFileChange" type="file" accept="image/*" ref="fileInput"
                    class="w-full text-dark-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gold-500 file:text-white file:font-semibold hover:file:bg-gold-600 file:cursor-pointer" />
                  <p class="text-dark-600 text-sm mt-2">Formato: JPG, PNG. Tamaño máximo: 2MB</p>
                </div>
                
                <div v-if="avatarError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                  {{ avatarError }}
                </div>
                
                <div v-if="avatarSuccess" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                  {{ avatarSuccess }}
                </div>
                
                <div class="flex gap-3">
                  <button type="submit" :disabled="!selectedFile || avatarLoading"
                    class="bg-gold-600 hover:bg-gold-700 text-white font-semibold py-3 px-6 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span v-if="avatarLoading">Subiendo...</span>
                    <span v-else>Subir Foto</span>
                  </button>
                  
                  <button v-if="barber?.avatar_url" @click.prevent="removeAvatar" type="button" :disabled="avatarLoading"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition disabled:opacity-50">
                    Eliminar Foto
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Acceso de Usuario Section -->
      <div v-if="!barber?.user" class="bg-white border border-blue-200 rounded-xl overflow-hidden shadow-lg">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
          <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            <h2 class="text-xl font-bold text-white">Crear Acceso al Panel</h2>
          </div>
          <p class="text-blue-100 text-sm mt-1">Este barbero aún no tiene acceso. Crea un usuario para que pueda ingresar.</p>
        </div>
        
        <div class="p-6">
          <form @submit.prevent="createUserAccess" class="space-y-4">
            <div>
              <label class="block text-dark-700 font-semibold mb-2">Email</label>
              <input v-model="userForm.email" type="email" required
                class="w-full bg-white border border-gold-300 text-dark-800 py-3 px-4 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="barbero@email.com" />
            </div>
            
            <div>
              <label class="block text-dark-700 font-semibold mb-2">Contraseña</label>
              <input v-model="userForm.password" type="password" required minlength="6"
                class="w-full bg-white border border-gold-300 text-dark-800 py-3 px-4 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Mínimo 6 caracteres" />
            </div>
            
            <div v-if="userError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
              {{ userError }}
            </div>
            
            <div v-if="userSuccess" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
              {{ userSuccess }}
            </div>
            
            <button type="submit" :disabled="userLoading"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition disabled:opacity-50">
              <span v-if="userLoading">Creando...</span>
              <span v-else>Crear Acceso</span>
            </button>
          </form>
        </div>
      </div>

      <!-- Cambiar Contraseña Section (si tiene usuario) -->
      <div v-else class="bg-white border border-green-200 rounded-xl overflow-hidden shadow-lg">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
          <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-xl font-bold text-white">Acceso Configurado</h2>
          </div>
        </div>
        
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div>
                <p class="text-dark-800 font-semibold">{{ barber.user.email }}</p>
                <p class="text-dark-600 text-sm">Puede ingresar a /barber/login</p>
              </div>
            </div>
            <button @click="showPasswordForm = !showPasswordForm"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
              {{ showPasswordForm ? 'Cancelar' : 'Cambiar Contraseña' }}
            </button>
          </div>

          <div v-if="showPasswordForm" class="pt-6 border-t border-green-200">
            <form @submit.prevent="changePassword" class="space-y-4 max-w-md">
              <div>
                <label class="block text-dark-700 font-semibold mb-2">Nueva Contraseña</label>
                <input v-model="passwordForm.password" type="password" required minlength="6"
                  class="w-full bg-white border border-gold-300 text-dark-800 py-3 px-4 rounded-lg focus:ring-2 focus:ring-blue-500 transition"
                  placeholder="Mínimo 6 caracteres" />
              </div>
              
              <div v-if="passwordError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                {{ passwordError }}
              </div>
              
              <div v-if="passwordSuccess" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ passwordSuccess }}
              </div>
              
              <button type="submit" :disabled="passwordLoading"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition disabled:opacity-50">
                <span v-if="passwordLoading">Cambiando...</span>
                <span v-else>Cambiar Contraseña</span>
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Horarios Section -->
      <div class="bg-white border border-gold-200 rounded-xl overflow-hidden shadow-lg">
        <div class="bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-4">
          <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-xl font-bold text-white">Configuración de Horarios</h2>
          </div>
          <p class="text-gold-100 text-sm mt-1">Define los horarios de trabajo y descansos para cada día</p>
        </div>
        
        <div class="p-6">
          <div class="grid lg:grid-cols-2 gap-4">
            <div v-for="day in weekdays" :key="day.value" 
              class="bg-gray-50 border border-gold-200 rounded-lg p-4 hover:border-gold-500 transition-all duration-300">
              <div class="flex items-center justify-between mb-3">
                <span class="text-dark-800 font-semibold text-lg">{{ day.label }}</span>
                <span class="text-xs text-dark-600">
                  {{ scheduleMap[day.value].start_time }} - {{ scheduleMap[day.value].end_time }}
                </span>
              </div>
              
              <div class="space-y-3">
                <div>
                  <label class="text-dark-600 text-xs uppercase tracking-wider mb-1 block">Horario de trabajo</label>
                  <div class="grid grid-cols-2 gap-2">
                    <div class="relative">
                      <input v-model="scheduleMap[day.value].start_time" type="time" 
                        class="w-full bg-white border border-gold-300 text-dark-800 py-2 px-3 rounded focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" />
                      <span class="absolute right-3 top-2.5 text-dark-500 text-xs">Inicio</span>
                    </div>
                    <div class="relative">
                      <input v-model="scheduleMap[day.value].end_time" type="time" 
                        class="w-full bg-white border border-gold-300 text-dark-800 py-2 px-3 rounded focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" />
                      <span class="absolute right-3 top-2.5 text-dark-500 text-xs">Fin</span>
                    </div>
                  </div>
                </div>
                
                <div>
                  <label class="text-dark-600 text-xs uppercase tracking-wider mb-1 block">Descanso (opcional)</label>
                  <div class="grid grid-cols-2 gap-2">
                    <input v-model="scheduleMap[day.value].break_start" type="time" 
                      placeholder="Inicio" 
                      class="bg-white border border-gold-300 text-dark-800 py-2 px-3 rounded focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" />
                    <input v-model="scheduleMap[day.value].break_end" type="time" 
                      placeholder="Fin" 
                      class="bg-white border border-gold-300 text-dark-800 py-2 px-3 rounded focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex justify-end mt-6 pt-4 border-t border-gold-200">
            <button @click="saveSchedules" 
              class="bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Guardar Horarios
            </button>
          </div>
        </div>
      </div>

      <!-- Ingresos Section -->
      <div class="bg-white border border-gold-200 rounded-xl overflow-hidden shadow-lg">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
          <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-xl font-bold text-white">Análisis de Ingresos</h2>
          </div>
          <p class="text-green-100 text-sm mt-1">Calcula los ingresos generados en un período específico</p>
        </div>
        
        <div class="p-6">
          <div class="flex flex-wrap gap-3 mb-6">
            <div class="flex-1 min-w-[200px]">
              <label class="text-dark-600 text-xs uppercase tracking-wider mb-2 block">Fecha desde</label>
              <input v-model="filters.from" type="date" 
                class="w-full bg-white border border-gold-300 text-dark-800 py-3 px-4 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" />
            </div>
            <div class="flex-1 min-w-[200px]">
              <label class="text-dark-600 text-xs uppercase tracking-wider mb-2 block">Fecha hasta</label>
              <input v-model="filters.to" type="date" 
                class="w-full bg-white border border-gold-300 text-dark-800 py-3 px-4 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" />
            </div>
            <div class="flex items-end">
              <button @click="loadEarnings" 
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Calcular
              </button>
            </div>
          </div>
          
          <div v-if="earnings" class="grid md:grid-cols-3 gap-4">
            <div class="bg-gray-50 border border-gray-300 rounded-lg p-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-dark-600 text-sm font-medium">Turnos Atendidos</span>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div class="text-3xl font-bold text-dark-800">{{ earnings?.appointments || 0 }}</div>
              <div class="text-dark-500 text-xs mt-1">servicios completados</div>
            </div>
            
            <div class="bg-gray-50 border border-gray-300 rounded-lg p-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-dark-600 text-sm font-medium">Promedio por Turno</span>
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
              </div>
              <div class="text-3xl font-bold text-dark-800">
                ${{ earnings?.appointments > 0 ? (earnings.total / earnings.appointments).toFixed(2) : '0.00' }}
              </div>
              <div class="text-dark-500 text-xs mt-1">ingreso promedio</div>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-300 rounded-lg p-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-dark-600 text-sm font-medium">Total Generado</span>
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="text-4xl font-bold text-green-700">
                ${{ earnings?.total?.toFixed(2) || '0.00' }}
              </div>
              <div class="text-green-600 text-xs mt-1">ingresos totales</div>
            </div>
          </div>
          
          <div v-else class="text-center py-12 text-dark-600">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p>Selecciona un rango de fechas y presiona Calcular</p>
          </div>
        </div>
      </div>

      <!-- Turnos Section -->
      <div class="bg-white border border-gold-200 rounded-xl overflow-hidden shadow-lg">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
          <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h2 class="text-xl font-bold text-white">Historial de Turnos</h2>
          </div>
          <p class="text-blue-100 text-sm mt-1">Consulta y filtra los turnos asignados</p>
        </div>
        
        <div class="p-6">
          <div class="flex flex-wrap gap-3 mb-6">
            <div class="flex-1 min-w-[180px]">
              <label class="text-dark-600 text-xs uppercase tracking-wider mb-2 block">Desde</label>
              <input v-model="filters.from" type="date" 
                class="w-full bg-white border border-gold-300 text-dark-800 py-3 px-4 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>
            <div class="flex-1 min-w-[180px]">
              <label class="text-dark-600 text-xs uppercase tracking-wider mb-2 block">Hasta</label>
              <input v-model="filters.to" type="date" 
                class="w-full bg-white border border-gold-300 text-dark-800 py-3 px-4 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>
            <div class="flex-1 min-w-[180px]">
              <label class="text-dark-600 text-xs uppercase tracking-wider mb-2 block">Estado</label>
              <select v-model="filters.status" 
                class="w-full bg-white border border-gold-300 text-dark-800 py-3 px-4 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                <option value="">Todos los estados</option>
                <option value="PENDING">Pendiente</option>
                <option value="CONFIRMED">Confirmado</option>
                <option value="CANCELLED">Cancelado</option>
                <option value="DONE">Atendido</option>
              </select>
            </div>
            <div class="flex items-end">
              <button @click="loadAppointments" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filtrar
              </button>
            </div>
          </div>
          
          <div v-if="appointments.length > 0" class="overflow-x-auto rounded-lg border border-gold-200">
            <table class="min-w-full">
              <thead>
                <tr class="bg-gray-100">
                  <th class="py-4 px-6 text-left text-xs font-semibold text-dark-700 uppercase tracking-wider">Fecha</th>
                  <th class="py-4 px-6 text-left text-xs font-semibold text-dark-700 uppercase tracking-wider">Hora</th>
                  <th class="py-4 px-6 text-left text-xs font-semibold text-dark-700 uppercase tracking-wider">Cliente</th>
                  <th class="py-4 px-6 text-left text-xs font-semibold text-dark-700 uppercase tracking-wider">Servicio</th>
                  <th class="py-4 px-6 text-left text-xs font-semibold text-dark-700 uppercase tracking-wider">Estado</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="a in appointments" :key="a.id" class="hover:bg-gold-50 transition">
                  <td class="py-4 px-6 text-dark-800 font-medium">{{ formatDate(a.starts_at) }}</td>
                  <td class="py-4 px-6 text-dark-700">{{ formatTime(a.starts_at) }}</td>
                  <td class="py-4 px-6 text-dark-700">{{ a.client_name }}</td>
                  <td class="py-4 px-6 text-dark-700">{{ a.service?.title }}</td>
                  <td class="py-4 px-6">
                    <span :class="getStatusClass(a.status)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                      {{ translateStatus(a.status) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-else class="text-center py-12 text-dark-600">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <p>No hay turnos que coincidan con los filtros seleccionados</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useBarbers } from '@/composables/useBarbers'

const route = useRoute()
const id = Number(route.params.id)

const { selectedBarber, loadBarber, saveSchedules: apiSaveSchedules, loadAppointments: apiLoadAppointments, loadEarnings: apiLoadEarnings } = useBarbers()

const barber = selectedBarber
const scheduleMap = reactive({
  0: { weekday: 0, start_time: '10:00', end_time: '19:00', break_start: '', break_end: '' },
  1: { weekday: 1, start_time: '10:00', end_time: '19:00', break_start: '', break_end: '' },
  2: { weekday: 2, start_time: '10:00', end_time: '19:00', break_start: '', break_end: '' },
  3: { weekday: 3, start_time: '10:00', end_time: '19:00', break_start: '', break_end: '' },
  4: { weekday: 4, start_time: '10:00', end_time: '19:00', break_start: '', break_end: '' },
  5: { weekday: 5, start_time: '10:00', end_time: '19:00', break_start: '', break_end: '' },
  6: { weekday: 6, start_time: '10:00', end_time: '19:00', break_start: '', break_end: '' },
})

const weekdays = [
  { value: 0, label: 'Domingo' },
  { value: 1, label: 'Lunes' },
  { value: 2, label: 'Martes' },
  { value: 3, label: 'Miércoles' },
  { value: 4, label: 'Jueves' },
  { value: 5, label: 'Viernes' },
  { value: 6, label: 'Sábado' },
]

const filters = reactive({ from: '', to: '', status: '' })
const appointments = ref([])
const earnings = ref(null)

// User access form
const userForm = reactive({ email: '', password: '' })
const userLoading = ref(false)
const userError = ref(null)
const userSuccess = ref(null)

// Password change form
const showPasswordForm = ref(false)
const passwordForm = reactive({ password: '' })
const passwordLoading = ref(false)
const passwordError = ref(null)
const passwordSuccess = ref(null)

// Avatar upload
const selectedFile = ref(null)
const fileInput = ref(null)
const avatarLoading = ref(false)
const avatarError = ref(null)
const avatarSuccess = ref(null)
const previewUrl = ref(null)

function handleFileChange(event) {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      avatarError.value = 'El archivo es muy grande. Máximo 2MB'
      selectedFile.value = null
      previewUrl.value = null
      return
    }
    
    // Crear preview
    const reader = new FileReader()
    reader.onload = (e) => {
      previewUrl.value = e.target.result
    }
    reader.readAsDataURL(file)
    
    selectedFile.value = file
    avatarError.value = null
  }
}

async function uploadAvatar() {
  if (!selectedFile.value) return

  avatarLoading.value = true
  avatarError.value = null
  avatarSuccess.value = null

  try {
    const formData = new FormData()
    formData.append('avatar', selectedFile.value)

    const response = await fetch(`http://localhost:8000/api/admin/barbers/${id}/upload-avatar`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`
      },
      body: formData
    })

    if (!response.ok) throw new Error('Error al subir la foto')

    avatarSuccess.value = 'Foto actualizada exitosamente'
    selectedFile.value = null
    previewUrl.value = null
    if (fileInput.value) fileInput.value.value = ''
    
    setTimeout(async () => {
      await loadBarber(id)
      avatarSuccess.value = null
    }, 1500)
  } catch (error) {
    avatarError.value = error.message || 'Error al subir la foto'
  } finally {
    avatarLoading.value = false
  }
}

async function removeAvatar() {
  if (!confirm('¿Eliminar la foto de perfil?')) return

  avatarLoading.value = true
  avatarError.value = null

  try {
    const response = await fetch(`http://localhost:8000/api/admin/barbers/${id}/remove-avatar`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`
      }
    })

    if (!response.ok) throw new Error('Error al eliminar la foto')

    avatarSuccess.value = 'Foto eliminada exitosamente'
    
    setTimeout(async () => {
      await loadBarber(id)
      avatarSuccess.value = null
    }, 1500)
  } catch (error) {
    avatarError.value = error.message || 'Error al eliminar la foto'
  } finally {
    avatarLoading.value = false
  }
}

function applyScheduleDefaults() {
  const schedules = barber.value?.schedules || []
  schedules.forEach(s => {
    scheduleMap[s.weekday] = { ...scheduleMap[s.weekday], ...s }
  })
}

async function saveSchedules() {
  const list = Object.values(scheduleMap)
  await apiSaveSchedules(id, list)
  await loadBarber(id)
}

async function loadAppointments() {
  const res = await apiLoadAppointments(id, { ...filters })
  const rows = Array.isArray(res?.data?.data) ? res.data.data : (res?.data || [])
  appointments.value = rows
}

async function loadEarnings() {
  earnings.value = await apiLoadEarnings(id, { ...filters })
}

function formatDate(iso) { return new Date(iso).toLocaleDateString() }
function formatTime(iso) { return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }

function translateStatus(status) {
  const translations = {
    PENDING: 'Pendiente',
    CONFIRMED: 'Confirmado',
    CANCELLED: 'Cancelado',
    RESCHEDULED: 'Reprogramado',
    DONE: 'Atendido'
  }
  return translations[status] || status
}

function getStatusClass(status) {
  const classes = {
    PENDING: 'bg-yellow-100 text-yellow-700 border border-yellow-300',
    CONFIRMED: 'bg-green-100 text-green-700 border border-green-300',
    CANCELLED: 'bg-red-100 text-red-700 border border-red-300',
    RESCHEDULED: 'bg-blue-100 text-blue-700 border border-blue-300',
    DONE: 'bg-purple-100 text-purple-700 border border-purple-300'
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

async function createUserAccess() {
  userLoading.value = true
  userError.value = null
  userSuccess.value = null

  try {
    const response = await fetch(`http://localhost:8000/api/admin/barbers/${id}/create-user-access`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`
      },
      body: JSON.stringify(userForm)
    })

    if (!response.ok) throw new Error('Error al crear acceso')

    userSuccess.value = 'Acceso creado exitosamente'
    userForm.email = ''
    userForm.password = ''
    
    setTimeout(async () => {
      await loadBarber(id)
      userSuccess.value = null
    }, 1500)
  } catch (error) {
    userError.value = error.message || 'Error al crear acceso'
  } finally {
    userLoading.value = false
  }
}

async function changePassword() {
  passwordLoading.value = true
  passwordError.value = null
  passwordSuccess.value = null

  try {
    const response = await fetch(`http://localhost:8000/api/admin/barbers/${id}/change-password`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`
      },
      body: JSON.stringify(passwordForm)
    })

    if (!response.ok) throw new Error('Error al cambiar contraseña')

    passwordSuccess.value = 'Contraseña actualizada exitosamente'
    passwordForm.password = ''
    
    setTimeout(() => {
      showPasswordForm.value = false
      passwordSuccess.value = null
    }, 1500)
  } catch (error) {
    passwordError.value = error.message || 'Error al cambiar contraseña'
  } finally {
    passwordLoading.value = false
  }
}

onMounted(async () => {
  await loadBarber(id)
  applyScheduleDefaults()
  await loadAppointments()
  await loadEarnings()
})
</script>

<style scoped></style>
