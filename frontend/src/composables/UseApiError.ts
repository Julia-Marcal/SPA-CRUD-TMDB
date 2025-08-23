import { useNotifications } from './UseNotifications'
import type { AxiosError } from 'axios'

export interface ApiErrorResponse {
  message?: string
  msg?: string
  error?: boolean
  errors?: Record<string, string[]> | string
}

export const useApiError = () => {
  const { showError } = useNotifications()

  const handleApiError = (error: unknown, defaultMessage = 'Ocorreu um erro inesperado'): void => {
    console.error('API Error:', error)

    let errorMessage = defaultMessage

    if (error instanceof Error) {
      if ('response' in error) {
        const axiosError = error as AxiosError<ApiErrorResponse>
        const response = axiosError.response

        if (response?.data) {
          const data = response.data

          if (data.message) {
            errorMessage = data.message
          } else if (data.msg) {
            errorMessage = data.msg
          } else if (data.errors) {
            errorMessage = formatValidationErrors(data.errors)
          }
        } else {
          switch (response?.status) {
            case 400:
              errorMessage = 'Dados inválidos enviados'
              break
            case 401:
              errorMessage = 'Não autorizado. Faça login novamente'
              break
            case 403:
              errorMessage = 'Acesso negado'
              break
            case 404:
              errorMessage = 'Recurso não encontrado'
              break
            case 422:
              errorMessage = 'Dados de entrada inválidos'
              break
            case 429:
              errorMessage = 'Muitas tentativas. Tente novamente mais tarde'
              break
            case 500:
              errorMessage = 'Erro interno do servidor'
              break
            case 503:
              errorMessage = 'Serviço temporariamente indisponível'
              break
            default:
              errorMessage = `Erro ${response?.status}: ${response?.statusText || 'Erro desconhecido'}`
          }
        }
      } else {
        if (error.message.includes('Network Error')) {
          errorMessage = 'Erro de conexão. Verifique sua internet'
        } else if (error.message.includes('timeout')) {
          errorMessage = 'Tempo limite excedido. Tente novamente'
        } else {
          errorMessage = error.message
        }
      }
    }

    showError(errorMessage)
  }

  const formatValidationErrors = (errors: Record<string, string[]> | string): string => {
    if (typeof errors === 'string') {
      try {
        const parsedErrors = JSON.parse(errors)
        return formatValidationErrors(parsedErrors)
      } catch {
        return errors
      }
    }

    if (typeof errors === 'object' && errors !== null) {
      const errorMessages = Object.entries(errors)
        .map(([field, messages]) => {
          const fieldName = field.charAt(0).toUpperCase() + field.slice(1)
          const message = Array.isArray(messages) ? messages[0] : messages
          return `${fieldName}: ${message}`
        })
        .join(', ')

      return errorMessages || 'Erro de validação'
    }

    return 'Erro de validação'
  }

  return {
    handleApiError,
    formatValidationErrors
  }
}
