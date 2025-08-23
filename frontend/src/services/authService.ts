import apiClient from './api'
import type { LoginCredentials, RegisterCredentials, AuthResponse } from '@/types/auth'

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
