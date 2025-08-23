<template>
  <div class="home-container">
    <header class="header">
      <div class="header-content">
        <div class="logo">
          <h1>MovieHub</h1>
        </div>
        <div class="search-bar">
          <span class="p-input-icon-left">
            <i class="pi pi-search"></i>
            <InputText
              v-model="searchQuery"
              placeholder="Search movies..."
              class="search-input"
              @input="handleSearch"
            />
          </span>
        </div>
        <div class="user-menu">
          <Button
            icon="pi pi-user"
            class="p-button-rounded p-button-text user-button"
            @click="toggleUserMenu"
          />
          <Menu
            ref="userMenu"
            :model="userMenuItems"
            :popup="true"
            class="user-dropdown"
          />
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

      <section class="hero-section">
        <div class="hero-content">
          <h2>Discover Amazing Movies</h2>
          <p>Explore the latest releases and timeless classics</p>
          <div class="hero-buttons">
            <Button
              label="Trending Now"
              icon="pi pi-fire"
              class="hero-button primary"
              @click="loadTrendingMovies"
            />
            <Button
              label="Top Rated"
              icon="pi pi-star"
              class="hero-button secondary"
              @click="loadTopRatedMovies"
            />
          </div>
        </div>
      </section>

      <section class="categories-section">
        <h3>Categories</h3>
        <div class="categories-grid">
          <div
            v-for="category in categories"
            :key="category.id"
            class="category-card"
            @click="selectCategory(category)"
          >
            <div class="category-icon">
              <i :class="category.icon"></i>
            </div>
            <h4>{{ category.name }}</h4>
            <p>{{ category.description }}</p>
          </div>
        </div>
      </section>

      <section class="featured-section">
        <div class="section-header">
          <h3>Featured Movies</h3>
          <Button
            label="View All"
            icon="pi pi-arrow-right"
            class="p-button-text view-all-button"
            @click="viewAllMovies"
          />
        </div>
        <div class="movies-grid">
          <div
            v-for="movie in featuredMovies"
            :key="movie.id"
            class="movie-card"
            @click="viewMovie(movie)"
          >
            <div class="movie-poster">
              <img :src="getPosterUrl(movie.poster_path)" :alt="movie.title" />
              <div class="movie-overlay">
                <Button
                  icon="pi pi-play"
                  class="p-button-rounded play-button"
                  @click.stop="playMovie(movie)"
                />
              </div>
            </div>
            <div class="movie-info">
              <h4 class="movie-title">{{ movie.title }}</h4>
              <div class="movie-meta">
                <span class="movie-year">{{ formatReleaseDate(movie.release_date) }}</span>
                <span class="movie-rating">
                  <i class="pi pi-star-fill"></i>
                  {{ movie.vote_average.toFixed(1) }}
                </span>
              </div>
              <p class="movie-genre">{{ movie.genre_ids.map(id => genres.find(g => g.id === id)?.name).filter(Boolean).join(', ') }}</p>
            </div>
          </div>
        </div>
      </section>

      <section class="recent-section">
        <h3>Recent Releases</h3>
        <div class="recent-movies-grid">
          <div
            v-for="movie in recentMovies"
            :key="movie.id"
            class="recent-movie-item"
          >
            <div class="recent-movie-poster">
              <img :src="getPosterUrl(movie.poster_path, 'w185')" :alt="movie.title" />
            </div>
            <div class="recent-movie-details">
              <h4>{{ movie.title }}</h4>
              <p>{{ movie.genre_ids.map(id => genres.find(g => g.id === id)?.name).filter(Boolean).join(', ') }}</p>
              <div class="movie-actions">
                <Button
                  icon="pi pi-heart"
                  class="p-button-rounded p-button-text like-button"
                  @click="toggleLike(movie)"
                />
                <Button
                  icon="pi pi-bookmark"
                  class="p-button-rounded p-button-text bookmark-button"
                  @click="toggleBookmark(movie)"
                />
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>


    <footer class="footer">
      <div class="footer-content">
        <p>&copy; 2024 MovieHub. All rights reserved.</p>
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
import { moviesService, type Movie, type Genre } from '../services/moviesService'
import useMovieUtils from '../composables/UseMovieUtils'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const searchQuery = ref('')
const userMenu = ref()

interface Category {
  id: number
  name: string
  description: string
  icon: string
}

interface UserMenuItem {
  label?: string
  icon?: string
  command?: () => void
  separator?: boolean
}

const categories = ref<Category[]>([
  {
    id: 1,
    name: 'Action',
    description: 'Explosive thrillers',
    icon: 'pi pi-bolt'
  },
  {
    id: 2,
    name: 'Drama',
    description: 'Emotional stories',
    icon: 'pi pi-heart'
  },
  {
    id: 3,
    name: 'Comedy',
    description: 'Laugh out loud',
    icon: 'pi pi-smile'
  },
  {
    id: 4,
    name: 'Horror',
    description: 'Spine-chilling',
    icon: 'pi pi-exclamation-triangle'
  }
])

const genres = ref<Genre[]>([])
const featuredMovies = ref<Movie[]>([])
const recentMovies = ref<Movie[]>([])
const loading = ref(false)
const authStore = useAuthStore()

// movie utils
const { getPosterUrl, formatReleaseDate } = useMovieUtils()

const userMenuItems = ref<UserMenuItem[]>([
  {
    label: 'Profile',
    icon: 'pi pi-user',
    command: () => console.log('Profile clicked')
  },
  {
    label: 'Settings',
    icon: 'pi pi-cog',
    command: () => console.log('Settings clicked')
  },
  {
    separator: true
  },
  {
    label: 'Logout',
    icon: 'pi pi-power-off',
    command: () => handleLogout()
  }
])

const handleSearch = async () => {
  if (!searchQuery.value.trim()) return

  try {
    loading.value = true
    const searchResults = await moviesService.searchMovies({
      query: searchQuery.value.trim(),
      page: 1
    })
    // For now, just show results in console, you can implement a search results view later
    console.log('Search results:', searchResults)
  } catch (error) {
    console.error('Search failed:', error)
  } finally {
    loading.value = false
  }
}

const toggleUserMenu = (event: Event) => {
  userMenu.value.toggle(event)
}

const selectCategory = (category: Category) => {
  console.log('Selected category:', category.name)
  // TODO: Implement category filtering
}

const loadTrendingMovies = async () => {
  try {
    loading.value = true
    const trending = await moviesService.getTrendingMovies()
    featuredMovies.value = trending.slice(0, 4) // Show first 4 trending movies
  } catch (error) {
    console.error('Failed to load trending movies:', error)
  } finally {
    loading.value = false
  }
}

const loadTopRatedMovies = async () => {
  try {
    loading.value = true
    // For now, we'll use trending movies as top rated
    // You can implement a separate top rated endpoint later
    await loadTrendingMovies()
  } catch (error) {
    console.error('Failed to load top rated movies:', error)
  } finally {
    loading.value = false
  }
}

const viewAllMovies = () => {
  console.log('View all movies')
  // TODO: Navigate to movies list page
}

const viewMovie = (movie: Movie) => {
  console.log('Viewing movie:', movie.title)
  // TODO: Navigate to movie detail page
}

const playMovie = (movie: Movie) => {
  console.log('Playing movie:', movie.title)
  // TODO: Implement movie playback
}

const toggleLike = (movie: Movie) => {
  console.log('Toggling like for:', movie.title)
  // TODO: Implement like functionality
}

const toggleBookmark = (movie: Movie) => {
  console.log('Toggling bookmark for:', movie.title)
  // TODO: Implement bookmark functionality
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
    // Force logout even if API call fails
    authStore.clearAuth()
    router.push('/login')
  }
}

const loadInitialData = async () => {
  try {
    loading.value = true

    // Load genres
    const genresData = await moviesService.getMovieGenres()
    genres.value = genresData

    // Load trending movies for featured section
    await loadTrendingMovies()

    // Load some recent movies (for now, we'll use trending movies)
    const trending = await moviesService.getTrendingMovies()
    recentMovies.value = trending.slice(4, 8) // Show movies 5-8 as recent

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
</style>

<style scoped>
.home-container {
  min-height: 100vh;
  background: #1a1a2e;
  color: #ffffff;
}

.header {
  background: #2a2a3e;
  border-bottom: 1px solid #3a3a4e;
  padding: 16px 0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo h1 {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.search-bar {
  flex: 1;
  max-width: 400px;
  margin: 0 40px;
}

.search-input {
  width: 100%;
  background: #3a3a4e;
  border: 1px solid #4a4a5e;
  color: #ffffff;
  border-radius: 25px;
  padding: 12px 20px 12px 45px;
}

.user-button {
  color: #ffffff;
  background: transparent;
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.hero-section {
  text-align: center;
  padding: 80px 0;
  background: linear-gradient(135deg, #1e1e2e 0%, #2d2d44 100%);
  border-radius: 16px;
  margin: 40px 0;
}

.hero-content h2 {
  font-size: 48px;
  font-weight: 700;
  margin: 0 0 16px 0;
  background: linear-gradient(135deg, #ffffff 0%, #a0a0b0 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-content p {
  font-size: 18px;
  color: #a0a0b0;
  margin: 0 0 32px 0;
}

.hero-buttons {
  display: flex;
  gap: 16px;
  justify-content: center;
}

.hero-button {
  padding: 16px 32px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 8px;
}

.hero-button.primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.hero-button.secondary {
  background: transparent;
  border: 2px solid #667eea;
  color: #667eea;
}

.categories-section {
  margin: 60px 0;
}

.categories-section h3 {
  font-size: 28px;
  font-weight: 600;
  margin: 0 0 32px 0;
  text-align: center;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
}

.category-card {
  background: #2a2a3e;
  border-radius: 12px;
  padding: 32px 24px;
  text-align: center;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid #3a3a4e;
}

.category-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.category-icon {
  font-size: 48px;
  color: #667eea;
  margin-bottom: 16px;
}

.category-card h4 {
  font-size: 20px;
  font-weight: 600;
  margin: 0 0 8px 0;
}

.category-card p {
  color: #a0a0b0;
  margin: 0;
}

.featured-section {
  margin: 60px 0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
}

.section-header h3 {
  font-size: 28px;
  font-weight: 600;
  margin: 0;
}

.view-all-button {
  color: #667eea;
}

.movies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}

.movie-card {
  background: #2a2a3e;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid #3a3a4e;
}

.movie-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.movie-poster {
  position: relative;
  height: 400px;
  overflow: hidden;
}

.movie-poster img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.movie-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.movie-card:hover .movie-overlay {
  opacity: 1;
}

.play-button {
  background: #667eea;
  border: none;
  width: 60px;
  height: 60px;
}

.movie-info {
  padding: 20px;
}

.movie-title {
  font-size: 18px;
  font-weight: 600;
  margin: 0 0 12px 0;
}

.movie-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.movie-year {
  color: #a0a0b0;
  font-size: 14px;
}

.movie-rating {
  color: #ffd700;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 4px;
}

.movie-genre {
  color: #a0a0b0;
  font-size: 14px;
  margin: 0;
}

.recent-section {
  margin: 60px 0;
}

.recent-section h3 {
  font-size: 28px;
  font-weight: 600;
  margin: 0 0 32px 0;
}

.recent-movies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  background: #2a2a3e;
  border-radius: 12px;
  padding: 24px;
  border: 1px solid #3a3a4e;
}

.recent-movie-item {
  background: #3a3a4e;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
}

.recent-movie-poster {
  margin-bottom: 16px;
}

.recent-movie-poster img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
}

.recent-movie-details h4 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 8px 0;
}

.recent-movie-details p {
  color: #a0a0b0;
  font-size: 14px;
  margin: 0 0 16px 0;
}

.movie-actions {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.like-button, .bookmark-button {
  color: #a0a0b0;
}

.like-button:hover, .bookmark-button:hover {
  color: #667eea;
}

/* Footer */
.footer {
  background: #2a2a3e;
  border-top: 1px solid #3a3a4e;
  padding: 32px 0;
  margin-top: 80px;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  text-align: center;
}

.footer-content p {
  color: #a0a0b0;
  margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 16px;
  }

  .search-bar {
    margin: 0;
    max-width: 100%;
  }

  .hero-content h2 {
    font-size: 32px;
  }

  .hero-buttons {
    flex-direction: column;
    align-items: center;
  }

  .categories-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }

  .movies-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(26, 26, 46, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  text-align: center;
  color: #ffffff;
}

.loading-spinner p {
  margin-top: 16px;
  color: #a0a0b0;
}

/* PrimeVue Component Overrides */
:deep(.p-inputtext) {
  background: #3a3a4e;
  border: 1px solid #4a4a5e;
  color: #ffffff;
}

:deep(.p-inputtext:focus) {
  border-color: #667eea;
  box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
}

:deep(.p-button.p-button-text) {
  color: #667eea;
}

:deep(.p-button.p-button-text:hover) {
  background: rgba(102, 126, 234, 0.1);
}

:deep(.p-dataview) {
  background: transparent;
}

:deep(.p-paginator) {
  background: transparent;
  border: none;
  color: #ffffff;
}

:deep(.p-paginator .p-paginator-pages .p-paginator-page) {
  background: #3a3a4e;
  border: 1px solid #4a4a5e;
  color: #ffffff;
}

:deep(.p-paginator .p-paginator-pages .p-paginator-page.p-highlight) {
  background: #667eea;
  border-color: #667eea;
}

:deep(.p-paginator .p-paginator-pages .p-paginator-page:hover) {
  background: #4a4a5e;
  border-color: #4a4a5e;
}
</style>
