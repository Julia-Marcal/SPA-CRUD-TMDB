<template>
  <div class="home-container">
    <header class="header">
      <div class="header-content">
        <div class="logo">
          <h1>MovieHub</h1>
        </div>
        <div class="search-bar">
          <InputText v-model="searchQuery" placeholder="Procure filmes..." class="search-input" @input="handleSearch" />
        </div>
        <div class="user-menu">
          <Button icon="pi pi-user" class="p-button-rounded p-button-text user-button" @click="toggleUserMenu"
            aria-haspopup="true" aria-controls="userMenuPopup" />
          <Menu ref="userMenu" id="userMenuPopup" :model="userMenuItems" :popup="true" class="user-dropdown">
            <template #item="{ item, props }">
                <a v-bind="props.action" class="p-menuitem-link"
                style="margin:.75rem;display:flex;align-items:center;gap:.5rem;padding:.6rem .85rem;border:1px solid var(--surface-border);border-radius:.6rem;background:var(--surface-card);box-shadow:0 2px 6px rgba(0,0,0,.08);transition:.15s; font-weight:500;">
                <i :class="item.icon" />
                <span class="p-menuitem-text">{{ item.label }}</span>
                </a>
            </template>
          </Menu>
        </div>
      </div>
    </header>

    <main class="main-content">
      <div v-if="loading" class="loading-overlay">
        <div class="loading-spinner">
          <i class="pi pi-spin pi-spinner" style="font-size: 2rem;"></i>
          <p>Loading...</p>
        </div>
      </div>

      <section>
        <div class="section-header">
          <h3>Filmes em Destaque</h3>
          <Button label="Ver Todos" class="p-button-text view-all-button" @click="viewAllMovies" />
        </div>
        <div class="movies-grid">
          <MovieCard v-for="movie in featuredMovies" :key="movie.id" :movie="movie" :genres="genres"
            @click="viewMovie(movie)" />
        </div>
      </section>

      <section v-if="favoriteMovies.length > 0" class="favorite-movies-section">
        <div class="section-header">
          <h3>Seus Filmes Favoritos</h3>
          <Button label="Ver Todos" class="p-button-text view-all-button" @click="viewAllFavorites" />
        </div>
        <div class="movies-grid">
          <MovieCard v-for="movie in favoriteMovies" :key="movie.id" :movie="movie" :genres="genres"
            @click="viewMovie(movie)" />

          <PlaceholderCard v-for="n in Math.max(0, 3 - favoriteMovies.length)" :key="'placeholder-' + n"
            @explore="viewAllMovies" />
        </div>
      </section>


      <section class="categories-section">
        <h3>Categorias</h3>
        <div class="categories-grid">
          <div v-for="category in categories" :key="category.id" class="category-card"
            @click="selectCategory(category)">
            <h4>{{ category.name }}</h4>
            <p>{{ category.description }}</p>
          </div>
        </div>
      </section>
    </main>


    <footer class="footer">
      <div class="footer-content">
        <p>&copy; 2024 MovieHub. Todos os direitos reservados.</p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Menu from 'primevue/menu'
import { moviesService } from '../services/moviesService'
import { type Movie, type Genre, type FavoriteMovie } from '../types/movies'
import { useAuthStore } from '@/stores/auth'
import MovieCard from './ui/MovieCard.vue'
import PlaceholderCard from './ui/PlaceholderCard.vue'

const router = useRouter()
const searchQuery = ref('')
const userMenu = ref()

interface Category {
  id: number
  name: string
  description: string
}

interface UserMenuItem {
  label?: string
  icon?: string
  command?: () => void
  separator?: boolean
}

const genreDetails: { [key: string]: { description: string } } = {
  Ação: { description: 'Filmes cheios de ação e adrenalina' },
  Aventura: { description: 'Jornadas emocionantes e exploratórias' },
  Animação: { description: 'Mundos animados e criativos' },
  Comédia: { description: 'Filmes para rir e se divertir' },
  Crime: { description: 'Histórias sobre o submundo do crime' },
  Documentário: { description: 'Histórias reais e informativas' },
  Drama: { description: 'Narrativas emocionantes e profundas' },
  Família: { description: 'Filmes para todas as idades' },
}

const categories = ref<Category[]>([])

const genres = ref<Genre[]>([])
const featuredMovies = ref<Movie[]>([])
const favoriteMovies = ref<FavoriteMovie[]>([])
const loading = ref(false)
const authStore = useAuthStore()
let debounceTimer: number

const userMenuItems = ref<UserMenuItem[]>([
  {
    separator: true
  },
  {
    label: 'Logout',
    icon: 'pi pi-power-off',
    command: () => handleLogout()
  }
])

const handleSearch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(async () => {
    if (!searchQuery.value.trim()) return

    router.push({ name: 'search', query: { q: searchQuery.value.trim() } })
  }, 1000)
}

const toggleUserMenu = (event: Event) => {
  userMenu.value.toggle(event)
}

const selectCategory = (category: Category) => {
  router.push({ name: 'genre', params: { id: category.id } })
}

const loadTrendingMovies = async () => {
  try {
    loading.value = true
    const trending = await moviesService.getTrendingMovies()
    featuredMovies.value = trending.slice(0, 6)
  } catch (error) {
    console.error('Failed to load trending movies:', error)
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const apiGenres = await moviesService.getMovieGenres()
    genres.value = apiGenres

    categories.value = apiGenres.map(genre => {
      const details = genreDetails[genre.name]
      return {
        id: genre.id,
        name: genre.name,
        description: details ? details.description : 'Explore filmes deste gênero.'
      }
    }).slice(0, 8)
  } catch (error) {
    console.error('Failed to load categories:', error)
  }
}

const loadFavoriteMovies = async () => {
  try {
    const response = await moviesService.getFavoriteMovies()
    favoriteMovies.value = response.movies.slice(0, 3)
  } catch (error) {
    console.error('Failed to load favorite movies:', error)
  }
}

const viewAllMovies = () => {
  router.push('/trending')
}

const viewAllFavorites = () => {
  router.push({ name: 'favorites' })
}

const viewMovie = (movie: Movie) => {
  router.push({ name: 'movie-detail', params: { id: movie.id } })
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
    authStore.clearAuth()
    router.push('/login')
  }
}

const loadInitialData = async () => {
  try {
    loading.value = true

    await Promise.all([
      loadCategories(),
      loadTrendingMovies(),
      loadFavoriteMovies()
    ])

  } catch (error) {
    console.error('Failed to load initial data:', error)
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  const isAuthenticated = authStore.isAuthenticated
  if (!isAuthenticated) {
    router.push('/login')
    return
  }

  await loadInitialData()
})
</script>

<style>
@import '../assets/styles/main.css';
@import '../assets/styles/home.css';
</style>
