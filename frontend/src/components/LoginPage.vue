<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>Bem-vindo de volta</h1>
        <p>Faça login em sua conta</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
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

        <Button
          type="submit"
          label="Entrar"
          class="w-full login-button"
          :loading="authStore.isLoading"
          :disabled="authStore.isLoading || !form.email || !form.password"
        />
      </form>

      <div class="login-footer">
        <p>Não tem uma conta? <a href="#" @click="goToRegister">Cadastre-se</a></p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import { useAuthStore } from '@/stores/auth'
import { useApiError } from '@/composables/UseApiError'
import { useNotifications } from '@/composables/UseNotifications'
import { useFormValidation } from '@/composables/UseFormValidation'

const router = useRouter()
const authStore = useAuthStore()
const { handleApiError } = useApiError()
const { showSuccess } = useNotifications()

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const commonRules = {
  email: [
    { required: true, message: 'Email é obrigatório' },
    { pattern: /^\S+@\S+\.\S+$/, message: 'Email inválido' }
  ],
  password: [
    { required: true, message: 'Senha é obrigatória' },
    { minLength: 8, message: 'A senha deve ter pelo menos 8 caracteres' }
  ]
}

const validationRules = {
  email: commonRules.email,
  password: commonRules.password
}

const { errors, validate, validateSingleField } = useFormValidation(validationRules)

const handleLogin = async () => {
  if (!validate(form)) return

  try {
    await authStore.login({
      email: form.email,
      password: form.password
    })

    showSuccess('Login realizado com sucesso!')
    await nextTick()
    router.push('/home')
  } catch (error: unknown) {
    handleApiError(error, 'Falha no login. Tente novamente.')
  }
}

const handleFieldBlur = (fieldName: string, value: any) => {
  validateSingleField(fieldName, value)
}

const goToRegister = () => {
  router.push('/register')
}
</script>

<style scoped>
  @import '../assets/styles/login.css';
</style>
