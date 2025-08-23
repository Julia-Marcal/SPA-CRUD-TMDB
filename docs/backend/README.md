# 🎬 SPA CRUD TMDB - Backend Laravel

API REST para integração com The Movie Database (TMDB) usando Laravel com arquitetura limpa.

## 🚀 Funcionalidades

- **Busca de Filmes**: Pesquisa por título com paginação
- **Gêneros**: Lista de gêneros disponíveis
- **Filmes em Alta**: Trending do dia
- **Detalhes do Filme**: Informações completas

## 🏗️ Arquitetura

- **Service Layer**: Lógica de negócio encapsulada
- **DTOs**: Objetos para transferência de dados
- **Cache Inteligente**: TTL configurável para respostas
- **Tratamento de Erros**: Exceções customizadas e logging
- **HTTP Client**: Guzzle com retry logic e timeouts

## 📁 Estrutura

```
app/
├── Contracts/           # Interfaces
├── DTOs/               # Data Transfer Objects
├── Exceptions/         # Exceções customizadas
├── Http/Controllers/   # Controladores
├── Providers/          # Service Providers
└── Services/           # Camada de serviços
```

## ⚙️ Configuração

### .env
```env
TMDB_BASE_URL=https://api.themoviedb.org/3
TMDB_BEARER_TOKEN=your_token
TMDB_LANGUAGE=pt-BR
TMDB_TIMEOUT=30
TMDB_CACHE_TTL=3600
```

## 🔧 Endpoints

- `GET /api/movies/search?query={title}&page={page}` - Buscar filmes
- `GET /api/movies/genres` - Listar gêneros
- `GET /api/movies/trending` - Filmes em alta
- `GET /api/movies/{id}` - Detalhes do filme

## 🚀 Execução

### Docker
```bash
docker-compose up -d
docker-compose exec backend php artisan migrate
# Acesse: http://localhost:8080
```

### Local
```bash
composer install
cp .env.example .env
php artisan migrate
php artisan serve
```

## 🧪 Testes

```bash
php artisan test
```

## 🔒 Segurança

- Autenticação JWT
- Validação de entrada
- Rate limiting
- Credenciais em variáveis de ambiente

