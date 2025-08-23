<template>
  <div class="search-bar">
    <span class="p-input-icon-left">
      <i class="pi pi-search"></i>
      <InputText
        :model-value="modelValue"
        :placeholder="placeholder"
        class="search-input"
        @input="handleInput"
        @keyup.enter="handleEnter"
      />
    </span>
  </div>
</template>

<script setup lang="ts">
import InputText from 'primevue/inputtext'

interface Props {
  modelValue: string
  placeholder?: string
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Buscar...'
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'search': [query: string]
  'enter': [query: string]
}>()

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
  emit('search', target.value)
}

const handleEnter = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('enter', target.value)
}
</script>

<style>
@import '../../assets/styles/searchBar.css';
</style>
