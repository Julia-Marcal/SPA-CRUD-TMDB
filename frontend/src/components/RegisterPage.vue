<template>
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h1>Crie sua conta</h1>
        <p>É rápido e fácil.</p>
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <label for="name">Nome</label>
          <InputText
            id="name"
            v-model="form.name"
            placeholder="Digite seu nome"
            class="w-full"
            :class="{ 'p-invalid': errors.name }"
            @blur="handleFieldBlur('name', form.name)"
          />
          <small v-if="errors.name" class="p-error">{{ errors.name }}</small>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <InputText
            id="email"
            v-model="form.email"
            type="email"
            placeholder="Digite seu email"
            class="w-full"
            :class="{ 'p-invalid': errors.email }"
            @blur="handleFieldBlur('email', form.email)"
          />
          <small v-if="errors.email" class="p-error">{{ errors.email }}</small>
        </div>

        <div class="form-group">
          <label for="password">Senha</label>
          <Password
            id="password"
            v-model="form.password"
            placeholder="Digite sua senha"
            class="w-full"
            :class="{ 'p-invalid': errors.password }"
            :feedback="false"
            @blur="handleFieldBlur('password', form.password)"
          />
          <small v-if="errors.password" class="p-error">{{ errors.password }}</small>
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirme a Senha</label>
          <Password
            id="password_confirmation"
            v-model="form.password_confirmation"
            placeholder="Confirme sua senha"
            class="w-full"
            :class="{ 'p-invalid': errors.password_confirmation }"
            :feedback="false"
            @blur="handleFieldBlur('password_confirmation', form.password_confirmation)"
          />
          <small v-if="errors.password_confirmation" class="p-error">{{ errors.password_confirmation }}</small>
        </div>

        <Button
          type="submit"
          label="Cadastrar"
          class="w-full register-button"
          :loading="isLoading"
          :disabled="isLoading || !form.name || !form.email || !form.password || !form.password_confirmation"
        />
      </form>

      <div class="register-footer">
        <p>Já tem uma conta? <a href="#" @click="goToLogin">Faça login</a></p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import { useFormValidation } from '@/composables/UseFormValidation'
import { useApiError } from '@/composables/UseApiError'
import { useNotifications } from '@/composables/UseNotifications'
import { authService } from '@/services/authService'

const router = useRouter()
const { handleApiError } = useApiError()
const { showSuccess } = useNotifications()
const isLoading = ref(false)

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const validationRules = {
  name: [{ required: true, message: 'Nome é obrigatório' }],
  email: [
    { required: true, message: 'Email é obrigatório' },
    { pattern: /^\S+@\S+\.\S+$/, message: 'Email inválido' }
  ],
  password: [
    { required: true, message: 'Senha é obrigatória' },
    { minLength: 8, message: 'A senha deve ter pelo menos 8 caracteres' }
  ],
  password_confirmation: [
    { required: true, message: 'Confirmação de senha é obrigatória' },
    {
      custom: (value: any) => {
        if (value !== form.password) {
          return 'As senhas não conferem'
        }
        return null
      }
    }
  ]
}

const { errors, validate, validateSingleField } = useFormValidation(validationRules)

const handleRegister = async () => {
  if (!validate(form)) return

  isLoading.value = true
  try {
    await authService.register(form)
    showSuccess('Cadastro realizado com sucesso! Faça o login para continuar.')
    router.push('/login')
  } catch (error: unknown) {
    handleApiError(error, 'Falha no cadastro. Tente novamente.')
  } finally {
    isLoading.value = false
  }
}

const handleFieldBlur = (fieldName: string, value: any) => {
  validateSingleField(fieldName, value)
}

const goToLogin = () => {
  router.push('/login')
}
</script>

<style scoped>
  @import '../assets/styles/login.css';
  @import '../assets/styles/register.css';
</style>

