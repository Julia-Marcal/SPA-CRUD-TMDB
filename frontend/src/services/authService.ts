import apiClient from './api'
import type { User } from '@/stores/auth'

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterCredentials extends LoginCredentials {
  name: string
  last_name: string
  age: number
}

export interface AuthResponse {
  token: string
  user: User
}

export const authService = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await apiClient.post<AuthResponse>('auth/login', credentials)
    return response.data
  },

  async register(credentials: RegisterCredentials): Promise<void> {
    await apiClient.post('auth/register', credentials)
  },

  async logout(): Promise<void> {
    await apiClient.post('auth/logout')
  }
}
