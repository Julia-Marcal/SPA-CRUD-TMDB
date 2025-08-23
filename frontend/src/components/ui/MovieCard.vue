<template>
  <div class="movie-card" @click="$emit('click', movie)">
    <div class="movie-poster">
      <img :src="posterUrl" :alt="movie.title" />
      <div class="movie-overlay">
        <Button
          icon="pi pi-play"
          class="p-button-rounded play-button"
          @click.stop="$emit('play', movie)"
        />
      </div>
    </div>
    <div class="movie-info">
      <h4 class="movie-title">{{ movie.title }}</h4>
      <div class="movie-meta">
        <span class="movie-year">{{ formattedReleaseDate }}</span>
        <span class="movie-rating">
          <i class="pi pi-star-fill"></i>
          {{ formattedVoteAverage }}
        </span>
      </div>
      <p class="movie-genre">{{ formattedGenres }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Button from 'primevue/button'
import type { Movie, Genre } from '@/services/moviesService'
import useMovieUtils from '@/composables/UseMovieUtils'

interface Props {
  movie: Movie
  genres?: Genre[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
  click: [movie: Movie]
  play: [movie: Movie]
}>()

const { getPosterUrl, formatReleaseDate, formatVoteAverage } = useMovieUtils()

const posterUrl = computed(() => getPosterUrl(props.movie.poster_path))

const formattedReleaseDate = computed(() => formatReleaseDate(props.movie.release_date))

const formattedVoteAverage = computed(() => formatVoteAverage(props.movie.vote_average))

const formattedGenres = computed(() => {
  if (!props.genres || !props.movie.genre_ids) return ''

  return props.movie.genre_ids
    .map(id => props.genres?.find(g => g.id === id)?.name)
    .filter(Boolean)
    .join(', ')
})
</script>

<style>
@import '../../assets/styles/movieCard.css';
</style>
