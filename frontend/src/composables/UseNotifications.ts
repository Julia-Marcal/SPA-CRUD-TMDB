import { useToast } from 'primevue/usetoast'

export interface NotificationOptions {
  title?: string
  detail?: string
  life?: number
  closable?: boolean
}

export const useNotifications = () => {
  const toast = useToast()

  const showSuccess = (message: string, options?: NotificationOptions) => {
    toast.add({
      severity: 'success',
      summary: options?.title || 'Sucesso',
      detail: options?.detail || message,
      life: options?.life || 3000,
      closable: options?.closable ?? true
    })
  }

  const showError = (message: string, options?: NotificationOptions) => {
    toast.add({
      severity: 'error',
      summary: options?.title || 'Erro',
      detail: options?.detail || message,
      life: options?.life || 5000,
      closable: options?.closable ?? true
    })
  }

  const showWarning = (message: string, options?: NotificationOptions) => {
    toast.add({
      severity: 'warn',
      summary: options?.title || 'Atenção',
      detail: options?.detail || message,
      life: options?.life || 4000,
      closable: options?.closable ?? true
    })
  }

  const showInfo = (message: string, options?: NotificationOptions) => {
    toast.add({
      severity: 'info',
      summary: options?.title || 'Informação',
      detail: options?.detail || message,
      life: options?.life || 3000,
      closable: options?.closable ?? true
    })
  }

  return {
    showSuccess,
    showError,
    showWarning,
    showInfo
  }
}

