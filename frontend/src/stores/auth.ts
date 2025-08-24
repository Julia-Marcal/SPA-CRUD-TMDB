import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { AuthResponse, LoginCredentials, RegisterCredentials } from '@/services/authService'
import { authService } from '@/services/authService'

export interface User {
  id: number
  name: string
  last_name: string
  email: string
  age: number
}

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)
  const isLoading = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const fullName = computed(() => {
    if (!user.value) return ''
    return `${user.value.name} ${user.value.last_name}`
  })

  const parseJwt = (token: string) => {
    try {
      const base64Url = token.split('.')[1]
      const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/')
      const jsonPayload = decodeURIComponent(
        atob(base64)
          .split('')
          .map(function (c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
          })
          .join('')
      )
      return JSON.parse(jsonPayload)
    } catch (e) {
      console.error('Invalid token', e)
      return null
    }
  }

  // Actions
  const initializeAuth = () => {
    const storedToken = localStorage.getItem('authToken')
    const storedUser = localStorage.getItem('user')
    const isAuth = localStorage.getItem('isAuthenticated')

    if (storedToken && storedUser && isAuth === 'true') {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  const login = async (credentials: LoginCredentials): Promise<void> => {
    isLoading.value = true
    try {
      const authData: AuthResponse = await authService.login(credentials)
      token.value = authData.token

      const tokenPayload = parseJwt(authData.token)
      if (!tokenPayload || !tokenPayload.sub) {
        throw new Error('Invalid token or missing user ID')
      }
      const userId = tokenPayload.sub

      user.value = authData.user

      localStorage.setItem('authToken', authData.token)
      localStorage.setItem('user', JSON.stringify(authData.user))
      localStorage.setItem('isAuthenticated', 'true')
    } finally {
      isLoading.value = false
    }
  }

  const register = async (credentials: RegisterCredentials): Promise<void> => {
    isLoading.value = true
    try {
      await authService.register(credentials)
    } finally {
      isLoading.value = false
    }
  }

  const logout = async (): Promise<void> => {
    isLoading.value = true
    try {
      await authService.logout()
    } finally {
      // Clear store state
      token.value = null
      user.value = null
      isLoading.value = false
    }
  }

  const clearAuth = (): void => {
    token.value = null
    user.value = null
    localStorage.removeItem('authToken')
    localStorage.removeItem('isAuthenticated')
    localStorage.removeItem('user')
  }

  return {
    // State
    user,
    token,
    isLoading,

    // Getters
    isAuthenticated,
    fullName,

    // Actions
    initializeAuth,
    login,
    register,
    logout,
    clearAuth
  }
})
