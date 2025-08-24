import { reactive, computed } from 'vue'

export interface ValidationRule {
  required?: boolean
  minLength?: number
  maxLength?: number
  pattern?: RegExp
  custom?: (value: any) => string | null
  message?: string
}

export interface ValidationRules {
  [key: string]: ValidationRule[]
}

export interface ValidationErrors {
  [key: string]: string
}

export const useFormValidation = (rules: ValidationRules) => {
  const errors = reactive<ValidationErrors>({})

  const validateField = (fieldName: string, value: any): string => {
    const fieldRules = rules[fieldName] || []

    for (const rule of fieldRules) {
      if (rule.required && (!value || (typeof value === 'string' && value.trim() === ''))) {
        return rule.message || `${fieldName} é obrigatório`
      }

      if (!value || (typeof value === 'string' && value.trim() === '')) {
        continue
      }

      if (rule.minLength && typeof value === 'string' && value.length < rule.minLength) {
        return rule.message || `${fieldName} deve ter pelo menos ${rule.minLength} caracteres`
      }

      if (rule.maxLength && typeof value === 'string' && value.length > rule.maxLength) {
        return rule.message || `${fieldName} deve ter no máximo ${rule.maxLength} caracteres`
      }

      if (rule.pattern && typeof value === 'string' && !rule.pattern.test(value)) {
        return rule.message || `${fieldName} tem formato inválido`
      }

      if (rule.custom) {
        const customError = rule.custom(value)
        if (customError) {
          return customError
        }
      }
    }

    return ''
  }

  const validate = (data: Record<string, any>): boolean => {
    let isValid = true

    Object.keys(errors).forEach(key => {
      delete errors[key]
    })

    Object.keys(rules).forEach(fieldName => {
      const error = validateField(fieldName, data[fieldName])
      if (error) {
        errors[fieldName] = error
        isValid = false
      }
    })

    return isValid
  }

  const validateSingleField = (fieldName: string, value: any): boolean => {
    const error = validateField(fieldName, value)
    if (error) {
      errors[fieldName] = error
      return false
    } else {
      delete errors[fieldName]
      return true
    }
  }

  const clearErrors = () => {
    Object.keys(errors).forEach(key => {
      delete errors[key]
    })
  }

  const hasErrors = computed(() => Object.keys(errors).length > 0)

  const getFieldError = (fieldName: string) => errors[fieldName] || ''

  return {
    errors,
    validate,
    validateSingleField,
    clearErrors,
    hasErrors,
    getFieldError
  }
}

export const commonRules = {
  email: [
    { required: true, message: 'Email é obrigatório' },
    {
      pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      message: 'Digite um email válido'
    }
  ],
  password: [
    { required: true, message: 'Senha é obrigatória' },
    { minLength: 6, message: 'Senha deve ter pelo menos 6 caracteres' }
  ],
  confirmPassword: (password: string) => [
    { required: true, message: 'Confirmação de senha é obrigatória' },
    {
      custom: (value: string) => value !== password ? 'Senhas não coincidem' : null
    }
  ],
  name: [
    { required: true, message: 'Nome é obrigatório' },
    { minLength: 2, message: 'Nome deve ter pelo menos 2 caracteres' },
    { maxLength: 50, message: 'Nome deve ter no máximo 50 caracteres' }
  ],
  age: [
    { required: true, message: 'Idade é obrigatória' },
    {
      custom: (value: number) => {
        const age = Number(value)
        if (isNaN(age) || age < 1 || age > 120) {
          return 'Idade deve ser um número entre 1 e 120'
        }
        return null
      }
    }
  ]
}

