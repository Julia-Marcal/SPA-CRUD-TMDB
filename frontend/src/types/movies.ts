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
  is_favorite?: boolean
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
  is_favorite?: boolean
}

export interface FavoriteMovie {
  id: number
  user_id: number
  movie_id: number
  movie: MovieDetail
  created_at: string
  updated_at: string
}

export interface FavoriteMoviesResponse {
  movies: FavoriteMovie[]
  genres: Genre[]
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
