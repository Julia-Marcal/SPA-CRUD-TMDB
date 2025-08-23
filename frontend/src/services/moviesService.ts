import apiClient from './api'
import type {
  Movie,
  MovieDetail,
  FavoriteMoviesResponse,
  Genre,
  SearchParams,
  SearchResponse,
  ApiResponse
} from '@/types/movies'

class MoviesService {
  async searchMovies(params: SearchParams): Promise<SearchResponse> {
    const response = await apiClient.get<ApiResponse<SearchResponse>>('/movies/search', {
      params
    })
    return response.data.data
  }

  async getMovieGenres(): Promise<Genre[]> {
    const response = await apiClient.get<ApiResponse<Genre[]>>('/movies/genres')
    return response.data.data
  }

  async getTrendingMovies(): Promise<Movie[]> {
    const response = await apiClient.get<ApiResponse<Movie[]>>('/movies/trending')
    return response.data.data
  }

  async getMovieDetails(id: number): Promise<MovieDetail> {
    const response = await apiClient.get<ApiResponse<MovieDetail>>(`/movies/${id}`)
    return response.data.data
  }

  async getFavoriteMovies(): Promise<FavoriteMoviesResponse> {
    const response = await apiClient.get<ApiResponse<FavoriteMoviesResponse>>('/movies/favorites')
    return response.data.data
  }

  async addFavoriteMovie(movieId: number): Promise<void> {
    await apiClient.post(`/movies/favorites/${movieId}`)
  }

  async removeFavoriteMovie(movieId: number): Promise<void> {
    await apiClient.delete(`/movies/favorites/${movieId}`)
  }

  async getMoviesByGenre(genreId: number): Promise<Movie[]> {
    const response = await apiClient.get<ApiResponse<Movie[]>>(`/movies/genre/${genreId}`)
    return response.data.data
  }
}

export const moviesService = new MoviesService()
