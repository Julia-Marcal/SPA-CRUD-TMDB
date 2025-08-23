import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../components/LoginPage.vue'
import HomePage from '../components/HomePage.vue'
import MoviesListPage from '../components/MoviesListPage.vue'
import { useAuthStore } from '@/stores/auth'
import RegisterPage from '../components/RegisterPage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/home'
    },
    {
      path: '/login',
      name: 'login',
      component: LoginPage
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterPage
    },
    {
      path: '/home',
      name: 'home',
      component: HomePage,
      meta: { requiresAuth: true }
    },
    {
      path: '/trending',
      name: 'trending',
      component: MoviesListPage,
      meta: { requiresAuth: true }
    },
    {
      path: '/search',
      name: 'search',
      component: MoviesListPage,
      meta: { requiresAuth: true }
    },
    {
      path: '/movie/:id',
      name: 'movie-detail',
      component: () => import('../components/MovieDetailPage.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/genre/:id',
      name: 'genre',
      component: MoviesListPage,
      meta: { requiresAuth: true }
    },
    {
      path: '/favorites',
      name: 'favorites',
      component: () => import('../components/FavoriteMoviesPage.vue'),
      meta: { requiresAuth: true }
    }
  ],
})

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    const authStore = useAuthStore()
    if (!authStore.isAuthenticated) {
      next({ name: 'login' })
      return
    }
  }
  next()
})

export default router
