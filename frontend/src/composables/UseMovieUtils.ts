export default function useMovieUtils() {
  const getPosterUrl = (posterPath: string | null, size = 'w500') => {
    if (posterPath && typeof posterPath === 'string') {
      const cleanPath = posterPath.startsWith('/') ? posterPath : `/${posterPath}`;
      return `https://image.tmdb.org/t/p/${size}${cleanPath}`;
    }
    return 'https://via.placeholder.com/500x750.png?text=No+Image';
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

  const getBackdropUrl = (backdropPath: string | null, size = 'w1280') => {
    if (backdropPath && typeof backdropPath === 'string') {
      const cleanPath = backdropPath.startsWith('/') ? backdropPath : `/${backdropPath}`;
      return `https://image.tmdb.org/t/p/${size}${cleanPath}`;
    }
    return '';
  }

  const formatCurrency = (amount: number) => {
    if (amount) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0
      }).format(amount)
    }
    return 'N/A'
  }

  return {
    getPosterUrl,
    formatReleaseDate,
    formatVoteAverage,
    getBackdropUrl,
    formatCurrency
  }
}
