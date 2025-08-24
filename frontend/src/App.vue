<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import Toast from 'primevue/toast'

const authStore = useAuthStore()

onMounted(() => {
  authStore.initializeAuth()
})
</script>

<template>
  <div id="app">
    <RouterView />
    <Toast />
  </div>
</template>

<style>
@import './assets/styles/app.css';
</style>
