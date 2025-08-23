<template>
  <div class="user-menu">
    <Button
      icon="pi pi-user"
      class="p-button-rounded p-button-text user-button"
      @click="toggleMenu"
    />
    <Menu
      ref="menu"
      :model="menuItems"
      :popup="true"
      class="user-dropdown"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import Button from 'primevue/button'
import Menu from 'primevue/menu'
import { useAuthStore } from '@/stores/auth'

const menu = ref()
const authStore = useAuthStore()

const emit = defineEmits<{
  logout: []
  profile: []
}>()

const menuItems = computed(() => [
  {
    label: authStore.fullName || 'Usuário',
    icon: 'pi pi-user',
    disabled: true,
    class: 'user-info'
  },
  {
    separator: true
  },
  {
    label: 'Perfil',
    icon: 'pi pi-user-edit',
    command: () => emit('profile')
  },
  {
    label: 'Configurações',
    icon: 'pi pi-cog',
    command: () => console.log('Settings clicked')
  },
  {
    separator: true
  },
  {
    label: 'Sair',
    icon: 'pi pi-sign-out',
    command: () => emit('logout')
  }
])

const toggleMenu = (event: Event) => {
  menu.value.toggle(event)
}
</script>

<style>
@import '../../assets/styles/userMenu.css';
</style>
