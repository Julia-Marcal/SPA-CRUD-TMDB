# SPA-CRUD-TMDB

Uma Single Page Application (SPA) para gerenciar uma lista de filmes favoritos, utilizando a API do The Movie Database (TMDB).

## Preview

![Movie Hub Preview](movie_hub_preview.png)

---

## Índice

- [Funcionalidades](#-funcionalidades)
- [Tecnologias](#-tecnologias)
- [Como Rodar o Projeto Localmente (Docker)](#-como-rodar-o-projeto-localmente-docker)
  - [1. Clonar o Repositório](#1-clonar-o-repositório)
  - [2. Configurar o Ambiente do Backend](#2-configurar-o-ambiente-do-backend)
  - [3. Subir os Contêineres](#3-subir-os-contêineres)
  - [4. Instalar Dependências e Gerar Chaves](#4-instalar-dependências-e-gerar-chaves)
  - [5. Configurar o Banco de Dados](#5-configurar-o-banco-de-dados)
  - [6. Acessar a Aplicação](#6-acessar-a-aplicação)
- [Como Testar a Aplicação](#-como-testar-a-aplicação)
- [Estrutura do Projeto (CRUD)](#-estrutura-do-projeto-crud)
- [Alternativo: Rodar o Frontend Separadamente](#-alternativo-rodar-o-frontend-separadamente)

---

## ● Funcionalidades

-   Navegar por uma lista de filmes populares.
-   Adicionar e remover filmes de uma lista de favoritos pessoal.
-   Gerenciar contas de usuário (login/cadastro).

## ● Tecnologias

| Camada | Tecnologia | Por que / Uso Principal |
|--------|------------|--------------------------|
| Backend | Laravel | API REST, migrations, validação, autenticação JWT |
| Frontend | Vue.js + Vite | SPA reativa, hot reload rápido |
| Banco de Dados | MySQL | Persistência relacional dos usuários e favoritos |
| Cache / Otimização | Redis | Cache de respostas externas (TMDB) e apoio a tokens/contagem |
| Containerização | Docker | Padronização do ambiente (PHP, Nginx, MySQL, Redis) |
| Autenticação | JWT (JSON Web Token) | Sessões stateless entre frontend e API |

## ● Como Rodar o Projeto Localmente (Docker)

Este guia descreve como configurar o ambiente de desenvolvimento completo com Docker.

### 1. Clonar o Repositório
```bash
git clone https://github.com/Julia-Marcal/SPA-CRUD-TMDB.git
cd SPA-CRUD-TMDB
```

### 2. Configurar o Ambiente do Backend

O backend precisa de um arquivo `.env` com as credenciais e a chave da API do TMDB.

**a. Copie o arquivo de exemplo:**
```bash
cd backend
cp .env.example .env
```

**b. Obtenha e configure a chave da API do TMDB:**
A aplicação requer uma chave da API do [The Movie Database (TMDB)](https://www.themoviedb.org) para funcionar.

-   **Crie uma conta:** Acesse o site e crie uma conta gratuita.
-   **Gere sua chave:** No painel da sua conta, vá em **Configurações > API** e solicite uma chave (v3 auth).
-   **Adicione a chave ao projeto:** Abra o arquivo `backend/.env` e insira sua chave na variável `TMDB_API_KEY`.
    ```env
    TMDB_API_KEY=SUA_CHAVE_DA_API_AQUI
    ```

### 3. Subir os Contêineres

Volte para a raiz do projeto e execute o comando para construir e iniciar os serviços.
```bash
# Na raiz do projeto
docker-compose up -d --build
```

### 4. Instalar Dependências e Gerar Chaves

Com os contêineres em execução, execute os seguintes comandos para finalizar a configuração do Laravel.

**a. Instalar dependências do Composer:**
```bash
docker-compose exec backend composer install
```

**b. Gerar a chave da aplicação:**
```bash
docker-compose exec backend php artisan key:generate
```

**c. Gerar o segredo JWT para autenticação:**
```bash
docker-compose exec backend php artisan jwt:secret
```
*(Se já existir um valor, o comando perguntará antes de substituir.)*

### 5. Configurar o Banco de Dados

Você pode popular o banco de dados de duas maneiras:

#### Método 1: Usando Migrations (Recomendado)
Este comando criará todas as tabelas necessárias.
```bash
docker-compose exec backend php artisan migrate
```

#### Método 2: Importando Dados de Exemplo (Dump SQL)
Se desejar um ambiente com usuários e favoritos pré-cadastrados, importe os arquivos `.sql`.

**a. Encontre o nome do contêiner MySQL:**
```bash
docker ps | grep mysql
```
O nome será algo como `spa-crud-tmdb-mysql-1`.

**b. Importe os arquivos (substitua `<container_name>`):**
```bash
# Importar tabela de usuários
docker exec -i <container_name> mysql -u root -proot spa_crud_tmdb < sql/movies_users.sql

# Importar tabela de filmes favoritos
docker exec -i <container_name> mysql -u root -proot spa_crud_tmdb < sql/movies_user_favorite_movies.sql
```

### 6. Acessar a Aplicação

A aplicação estará disponível nos seguintes endereços:
-   **Frontend:** `http://localhost:5174`
-   **Backend API:** `http://localhost:8080`

---

## ● Como Testar a Aplicação

### 1. Acesso à Interface
-   Abra o navegador e acesse `http://localhost:5174`.
-   Use um dos logins de teste:
    -   **Email:** `test@example.com` | **Senha:** `password`
    -   **Email:** `jooj3068@gmail.com` | **Senha:** `senhaaaa123`
    -   **Email:** `mu.540@example.com` | **Senha:** `senha123`
    -   **Email:** `usuario@example.com` | **Senha:** `senha_forte123`

### 2. Testes da API (Postman)
Para testar os endpoints da API, você pode usar a coleção do Postman disponível no link abaixo. Ela contém todas as rotas da aplicação, incluindo autenticação, gerenciamento de usuários e filmes.

-   [Acessar a Coleção do Postman](https://lively-satellite-334979.postman.co/workspace/SPA-CRUD-TMDB~21eba3c6-1bb1-436e-ab83-c4f30f63afc5/collection/25406751-4fcfe6bb-1a1c-42a9-8931-ec2d86c89327?action=share&creator=25406751)


---

## ● Estrutura do Projeto (CRUD)

A lógica da aplicação está dividida entre o backend (Laravel) e o frontend (Vue.js).

### Backend (API Laravel)
-   **Rotas:** `backend/routes/` (`MoviesRoutes.php` e `UsersRoutes.php`)
-   **Controllers:** `backend/app/Http/Controllers/` (`MoviesController.php` e `UsersController.php`)
-   **Serviços:** `backend/app/Services/` (onde a lógica de negócio reside, como `TMDBService.php`)
-   **Models:** `backend/app/Models/` (como `User.php` e `UserFavoriteMovie.php`)

### Frontend (Vue.js)
-   **Rotas:** `frontend/src/router/index.ts`
-   **Views (Páginas):** `frontend/src/components/` (como `HomePage.vue`, `FavoriteMoviesPage.vue`)
-   **Serviços (Chamadas API):** `frontend/src/services/moviesService.ts`
-   **Componentes de UI:** `frontend/src/components/ui/` (como `MovieCard.vue`)

---

## ● Alternativo: Rodar o Frontend Separadamente

Se preferir rodar o frontend (Vue.js) de forma independente do Docker:

1.  **Navegue até o diretório do frontend:**
    ```bash
    cd frontend
    ```

2.  **Instale as dependências:**
    ```bash
    npm install
    ```

3.  **Execute em modo de desenvolvimento:**
    ```bash
    npm run dev
    ```
    A aplicação estará disponível em `http://localhost:5173`.

**Importante:** Para que o frontend funcione, o backend (rodando via Docker) deve estar em execução para servir a API em `http://localhost:8080`.