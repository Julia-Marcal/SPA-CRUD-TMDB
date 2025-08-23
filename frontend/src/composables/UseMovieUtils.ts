export default function useMovieUtils() {
  const getPosterUrl = (posterPath: string | null, size = 'w500') => {
    if (posterPath) {
      return `https://image.tmdb.org/t/p/${size}${posterPath}`
    }
    return 'https://via.placeholder.com/500x750.png?text=No+Image'
  }

  const formatReleaseDate = (date: string) => {
    if (date) {
      return new Date(date).getFullYear().toString()
    }
    return 'N/A'
  }

  const formatVoteAverage = (voteAverage: number) => {
    if (voteAverage) {
      return voteAverage.toFixed(1)
    }
    return 'N/A'
  }

  return {
    getPosterUrl,
    formatReleaseDate,
    formatVoteAverage
  }
}
