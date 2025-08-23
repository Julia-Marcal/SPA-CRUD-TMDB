import apiClient from './api'

export interface Movie {
  id: number
  title: string
  overview: string
  poster_path: string
  backdrop_path: string
  release_date: string
  vote_average: number
  vote_count: number
  genre_ids: number[]
  adult: boolean
  video: boolean
  popularity: number
  media_type?: string
}

export interface MovieDetail extends Movie {
  genres: Genre[]
  runtime: number
  status: string
  tagline: string
  budget: number
  revenue: number
  production_companies: ProductionCompany[]
  spoken_languages: SpokenLanguage[]
  production_countries: ProductionCountry[]
}

export interface Genre {
  id: number
  name: string
}

export interface ProductionCompany {
  id: number
  name: string
  logo_path: string | null
  origin_country: string
}

export interface SpokenLanguage {
  english_name: string
  iso_639_1: string
  name: string
}

export interface ProductionCountry {
  iso_3166_1: string
  name: string
}

export interface SearchParams {
  query: string
  page?: number
  include_adult?: boolean
}

export interface SearchResponse {
  page: number
  results: Movie[]
  total_pages: number
  total_results: number
}

export interface ApiResponse<T> {
  data: T
}

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

  async getMovie(movieId: number): Promise<MovieDetail> {
    const response = await apiClient.get<ApiResponse<MovieDetail>>(`/movies/${movieId}`)
    return response.data.data
  }
}

export const moviesService = new MoviesService()
