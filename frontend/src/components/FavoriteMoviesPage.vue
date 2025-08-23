<template>
  <div class="movies-list-container">
    <header class="header">
      <div class="header-content">
        <Button icon="pi pi-arrow-left" class="p-button-text back-button" @click="goBack" />
        <h1>{{ pageTitle }}</h1>
        <div class="genre-filter">
          <Dropdown v-model="selectedGenre" :options="allGenres" optionLabel="name" placeholder="Filtrar por gênero"
            @change="filterMovies" class="genre-dropdown" panelClass="genre-dropdown-panel" />
        </div>
      </div>
    </header>

    <main class="main-content">
      <div v-if="loading" class="loading-overlay">
        <div class="loading-spinner">
          <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
          <p>Carregando...</p>
        </div>
      </div>

      <div v-else-if="filteredMovies.length === 0" class="no-results">
        <p>Nenhum filme favorito encontrado.</p>
      </div>

      <div v-else class="movies-grid">
        <MovieCard v-for="movie in filteredMovies" :key="movie.id" :movie="movie" :genres="allGenres"
          @click="viewMovie(movie)" />

        <PlaceholderCard v-for="n in Math.max(0, 3 - filteredMovies.length)" :key="'placeholder-' + n"
          @explore="viewAllMovies" />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { moviesService } from '../services/moviesService'
import { type Movie, type Genre, type FavoriteMovie } from '../types/movies'
import MovieCard from './ui/MovieCard.vue'
import PlaceholderCard from './ui/PlaceholderCard.vue'
import Button from 'primevue/button'
import Dropdown from 'primevue/dropdown'
import 'primeicons/primeicons.css'

const router = useRouter()

const loading = ref(false)
const allMovies = ref<FavoriteMovie[]>([])
const filteredMovies = ref<FavoriteMovie[]>([])
const allGenres = ref<Genre[]>([])
const selectedGenre = ref<Genre | null>(null)

const pageTitle = computed(() => 'Meus Filmes Favoritos')

const fetchFavoriteMovies = async () => {
  loading.value = true
  try {
    const response = await moviesService.getFavoriteMovies()
    if (response && response.movies) {
      allMovies.value = response.movies
      filteredMovies.value = allMovies.value
      allGenres.value = [{ id: 0, name: 'Todos' }, ...response.genres]
    }
  } catch (error) {
    console.error('Failed to fetch favorite movies:', error)
  } finally {
    loading.value = false
  }
}

const filterMovies = () => {
  if (selectedGenre.value && selectedGenre.value.id !== 0) {
    filteredMovies.value = allMovies.value.filter((movie) =>
      movie.genres.some((genre) => genre.id === selectedGenre.value?.id)
    )
  } else {
    filteredMovies.value = allMovies.value
  }
}

const viewMovie = (movie: Movie) => {
  router.push({ name: 'movie-detail', params: { id: movie.id } })
}

const goBack = () => {
  router.push('/home')
}

const viewAllMovies = () => {
  router.push('/trending')
}

onMounted(() => {
  fetchFavoriteMovies()
})

</script>

<style>
@import '../assets/styles/home.css';
@import '../assets/styles/favoriteMovies.css';
</style>
