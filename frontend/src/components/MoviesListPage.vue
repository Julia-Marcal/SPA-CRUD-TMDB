<template>
  <div class="movies-list-container">
    <header class="header">
      <div class="header-content">
        <Button icon="pi pi-arrow-left" class="p-button-text back-button" @click="goBack" />
        <h1>{{ pageTitle }}</h1>
        <div class="placeholder"></div>
      </div>
    </header>

    <main class="main-content">
      <div v-if="loading" class="loading-overlay">
        <div class="loading-spinner">
          <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
          <p>Carregando...</p>
        </div>
      </div>

      <div v-else-if="movies.length === 0 && isSearch" class="no-results">
        <p>Nenhum filme encontrado para "{{ searchQuery }}".</p>
      </div>

      <div v-else class="movies-grid">
        <MovieCard
          v-for="movie in movies"
          :key="movie.id"
          :movie="movie"
          :genres="genres"
          @click="viewMovie(movie)"
        />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { moviesService} from '../services/moviesService'
import { type Movie, type Genre } from '../types/movies'
import MovieCard from './ui/MovieCard.vue'
import Button from 'primevue/button'
import 'primeicons/primeicons.css'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const movies = ref<Movie[]>([])
const genres = ref<Genre[]>([])
const searchQuery = ref(route.query.q || '')

const isSearch = computed(() => route.name === 'search')
const isGenre = computed(() => route.name === 'genre')
const pageTitle = computed(() => {
  if (isSearch.value) {
    const genreIds = route.query.genre as string
    if (genreIds) {
      const genreId = parseInt(genreIds.split(',')[0], 10)
      const genre = genres.value.find((g) => g.id === genreId)
      if (genre) {
        return `Filmes de ${genre.name}`
      }
    }
    if (searchQuery.value) {
      return `Resultados para "${searchQuery.value}"`
    }
    return 'Filmes Populares'
  }
  if (isGenre.value) {
    const genre = genres.value.find((g) => g.id === parseInt(route.params.id as string))
    return genre ? `Filmes de ${genre.name}` : 'Filmes por Categoria'
  }
  return 'Filmes em Destaque'
})

const fetchData = async () => {
  loading.value = true
  try {
    const genresData = await moviesService.getMovieGenres()
    genres.value = genresData

    let moviesData: Movie[] = []
    if (isSearch.value) {
      const genreIds = route.query.genre as string
      if (genreIds) {
        const genreId = parseInt(genreIds.split(',')[0], 10)
        moviesData = await moviesService.getMoviesByGenre(genreId)
      } else if (searchQuery.value) {
        const response = await moviesService.searchMovies({
          query: searchQuery.value as string,
          page: 1
        })
        moviesData = response.results
      } else {
        moviesData = await moviesService.getTrendingMovies()
      }
    } else if (isGenre.value) {
      const genreId = parseInt(route.params.id as string)
      moviesData = await moviesService.getMoviesByGenre(genreId)
    } else {
      moviesData = await moviesService.getTrendingMovies()
    }
    movies.value = moviesData
  } catch (error) {
    console.error('Failed to fetch movies:', error)
  } finally {
    loading.value = false
  }
}

const viewMovie = (movie: Movie) => {
  router.push({ name: 'movie-detail', params: { id: movie.id } })
}

const goBack = () => {
  router.push('/home')
}

watch(
  () => route.query.q,
  (newQuery) => {
    if (isSearch.value) {
      searchQuery.value = newQuery || ''
      fetchData()
    }
  }
)

onMounted(() => {
  fetchData()
})
</script>

<style>
@import '../assets/styles/home.css';
</style>
