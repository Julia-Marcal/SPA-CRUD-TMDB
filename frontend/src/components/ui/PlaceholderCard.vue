<template>
  <div
    class="movie-card movie-card--placeholder"
    @click="$emit('explore')"
  >
    <div class="placeholder-image">
      <i class="pi pi-plus-circle"></i>
    </div>
    <div class="placeholder-content">
      <h4>Adicionar Favoritos</h4>
      <p>Você ainda tem espaço aqui. Toque para explorar mais filmes e marcar como favorito.</p>
      <div class="button-container">
        <Button
          label="Procurar filmes"
          size="small"
          class="p-button-text p-button-sm"
          @click.stop="$emit('explore')"
        />
      </div>
    </div>
    <span class="slot-badge">SLOT LIVRE</span>
    <div class="placeholder-gradient"></div>
  </div>
</template>

<script setup lang="ts">
import Button from 'primevue/button'

defineEmits(['explore'])
</script>

<style scoped>
@import '../../assets/styles/placeholderCard.css';
</style>

