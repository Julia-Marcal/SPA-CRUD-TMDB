<template>
  <div class="movie-detail-container">
    <Toast />
    <header class="header">
      <div class="header-content">
        <Button icon="pi pi-arrow-left" class="p-button-text back-button" @click="goBack" />
        <h1>{{ movie?.title }}</h1>
        <div class="placeholder"></div>
      </div>
    </header>

    <main class="main-content" v-if="movie">
      <div class="movie-backdrop" :style="{ backgroundImage: `url(${getBackdropUrl(movie.backdrop_path)})` }">
        <div class="movie-poster">
          <img :src="getPosterUrl(movie.poster_path)" :alt="movie.title" class="img-poster" />
        </div>
      </div>

      <div class="movie-info">
        <div class="title-with-favorite">
          <h2 class="movie-title">{{ movie.title }}</h2>
          <Button :icon="movie.is_favorite ? 'pi pi-heart-fill' : 'pi pi-heart'" :class="[
            'p-button-rounded',
            'p-button-danger',
            { 'p-button-outlined': !movie.is_favorite }
          ]" class="favorite-button" @click="toggleFavoriteStatus"
            v-tooltip.top="movie.is_favorite ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos'" />
        </div>
        <p class="tagline">{{ movie.tagline }}</p>
        <div class="genres">
          <span v-for="genre in movie.genres" :key="genre.id" class="genre-tag">{{ genre.name }}</span>
        </div>
        <p class="overview">{{ movie.overview }}</p>

        <div class="details-grid">
          <div class="detail-item">
            <strong>Lançamento:</strong>
            <span>{{ formatReleaseDate(movie.release_date) }}</span>
          </div>
          <div class="detail-item">
            <strong>Duração:</strong>
            <span>{{ movie.runtime }} min</span>
          </div>
          <div class="detail-item">
            <strong>Avaliação:</strong>
            <span>
              <i class="pi pi-star-fill"></i>
              {{ movie.vote_average.toFixed(1) }} ({{ movie.vote_count }} votos)
            </span>
          </div>
          <div class="detail-item">
            <strong>Orçamento:</strong>
            <span>{{ formatCurrency(movie.budget) }}</span>
          </div>
          <div class="detail-item">
            <strong>Receita:</strong>
            <span>{{ formatCurrency(movie.revenue) }}</span>
          </div>
          <div class="detail-item">
            <strong>Status:</strong>
            <span>{{ movie.status }}</span>
          </div>
        </div>
      </div>
    </main>
    <div v-else-if="loading" class="loading-overlay">
      <div class="loading-spinner">
        <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
        <p>Carregando detalhes do filme...</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { moviesService } from '../services/moviesService'
import { type MovieDetail } from '../types/movies'
import useMovieUtils from '../composables/UseMovieUtils'
import Button from 'primevue/button'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

const route = useRoute()
const router = useRouter()
const movie = ref<MovieDetail | null>(null)
const loading = ref(false)
const { getPosterUrl, getBackdropUrl, formatReleaseDate, formatCurrency } = useMovieUtils()
const toast = useToast()

const fetchMovieDetails = async () => {
  const movieId = Number(route.params.id)
  if (isNaN(movieId)) return

  loading.value = true
  try {
    movie.value = await moviesService.getMovieDetails(movieId)
  } catch (error) {
    console.error('Failed to load movie details:', error)
  } finally {
    loading.value = false
  }
}

const toggleFavoriteStatus = async () => {
  if (!movie.value) return
  loading.value = true
  try {
    if (movie.value.is_favorite) {
      await moviesService.removeFavoriteMovie(movie.value.id)
      toast.add({
        severity: 'success',
        summary: 'Sucesso',
        detail: 'Filme removido dos favoritos!',
        life: 3000
      })
      movie.value.is_favorite = false
    } else {
      await moviesService.addFavoriteMovie(movie.value.id)
      toast.add({
        severity: 'success',
        summary: 'Sucesso',
        detail: 'Filme adicionado aos favoritos!',
        life: 3000
      })
      movie.value.is_favorite = true
    }
  } catch (error) {
    console.error('Failed to update favorite status:', error)
    toast.add({
      severity: 'error',
      summary: 'Erro',
      detail: 'Não foi possível atualizar o status do filme.',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.go(-1)
}

onMounted(() => {
  fetchMovieDetails()
})
</script>

<style scoped>
@import '../assets/styles/movieDetailPage.css';
</style>
