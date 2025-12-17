import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import PublicLayout from '@/layouts/PublicLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

const routes = [
  {
    path: '/',
    component: PublicLayout,
    children: [
      {
        path: '',
        name: 'Home',
        component: () => import('@/pages/HomePage.vue')
      },
      {
        path: 'services',
        name: 'Services',
        component: () => import('@/pages/ServicesPage.vue')
      },
      {
        path: 'gallery',
        name: 'Gallery',
        component: () => import('@/pages/GalleryPage.vue')
      },
      {
        path: 'book',
        name: 'Book',
        component: () => import('@/pages/BookPage.vue')
      },
      {
        path: 'book/success',
        name: 'BookSuccess',
        component: () => import('@/pages/BookSuccessPage.vue')
      }
    ]
  },
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: () => import('@/pages/admin/LoginPage.vue')
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: () => import('@/pages/admin/DashboardPage.vue')
      },
      {
        path: 'bookings',
        name: 'AdminBookings',
        component: () => import('@/pages/admin/BookingsPage.vue')
      },
      {
        path: 'bookings/:id',
        name: 'AdminBookingDetail',
        component: () => import('@/pages/admin/BookingDetailPage.vue')
      },
      {
        path: 'services',
        name: 'AdminServices',
        component: () => import('@/pages/admin/ServicesPage.vue')
      }
      ,
      {
        path: 'barbers',
        name: 'admin-barbers',
        component: () => import('@/pages/admin/AdminBarbersList.vue')
      },
      {
        path: 'barbers/:id',
        name: 'admin-barber-detail',
        component: () => import('@/pages/admin/AdminBarberDetail.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    return { top: 0 }
  }
})

// Navigation guard
router.beforeEach(async (to, from, next) => {
  const { checkAuth } = useAuth()
  
  if (to.meta.requiresAuth) {
    const isAuth = await checkAuth()
    if (isAuth) {
      next()
    } else {
      next('/admin/login')
    }
  } else {
    next()
  }
})

export default router
