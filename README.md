# SPA-CRUD-TMDB

Esta é uma Single Page Application (SPA) para gerenciar uma lista de filmes favoritos, utilizando a API do The Movie Database (TMDB). O backend é construído com Laravel e o frontend com Vue.js.

A aplicação permite que os usuários naveguem por filmes, adicionem-nos a uma lista pessoal de favoritos e gerenciem sua conta de usuário.

## Preview

![Movie Hub Preview](movie_hub_preview.png)

---

## ● Como rodar o projeto localmente com Docker

Este passo a passo descreve como subir o ambiente de desenvolvimento completo utilizando Docker.

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/Julia-Marcal/SPA-CRUD-TMDB.git
    cd SPA-CRUD-TMDB
    ```

2.  **Configure o arquivo de ambiente do backend:**
    O backend precisa de um arquivo `.env` com as credenciais e a chave da API.
    ```bash
    cd backend
    cp .env.example .env
    ```

3.  **Obtenha e configure a chave da API do TMDB:**
    A aplicação requer uma chave da API do The Movie Database (TMDB) para funcionar.
    - **Crie uma conta:** Acesse o site oficial [The Movie Database (TMDB)](https://www.themoviedb.org) e crie uma conta gratuita.
    - **Gere sua chave:** No painel da sua conta, vá em "Configurações" > "API" e solicite uma chave de API (v3 auth).
    - **Adicione a chave ao projeto:** Abra o arquivo `backend/.env` e insira sua chave na variável `TMDB_API_KEY`.
      ```
      TMDB_API_KEY=SUA_CHAVE_DA_API_AQUI
      ```

4.  **Suba os contêineres Docker:**
    Volte para a raiz do projeto e execute o comando para construir e iniciar os serviços.
    ```bash
    # Na raiz do projeto
    docker-compose up -d --build
    ```

5.  **Gere a chave da aplicação Laravel:**
    Este comando é essencial para a segurança da aplicação.
    ```bash
    docker-compose exec backend php artisan key:generate
    ```

6.  **Acesse a interface web:**
    Após alguns instantes, a aplicação estará disponível:
    -   **Frontend:** `http://localhost:5174`
    -   **Backend API:** `http://localhost:8080`

---

## ● Como importar o banco de dados

Você pode configurar o banco de dados de duas maneiras:

### Método 1: Importando o Dump SQL (Recomendado)

Esta é a forma mais rápida de ter um ambiente com dados de exemplo.

1.  **Encontre o nome do contêiner MySQL:**
    ```bash
    docker ps | grep mysql
    ```
    O nome será algo como `spa-crud-tmdb-mysql-1`.

2.  **Importe os arquivos `.sql` (linha de comando):**  
  Execute os comandos abaixo na raiz do projeto, substituindo `<container_name>` pelo nome do seu contêiner MySQL (ex.: `spa-crud-tmdb-mysql-1`). A senha padrão é `root`.
  ```bash
  # Importar tabela de usuários
  docker exec -i <container_name> mysql -u root -proot spa_crud_tmdb < sql/movies_users.sql

  # Importar tabela de filmes favoritos
  docker exec -i <container_name> mysql -u root -proot spa_crud_tmdb < sql/movies_user_favorite_movies.sql
  ```

3.  **Verifique se os dados foram importados:**
  ```bash
  docker exec -it <container_name> mysql -u root -proot -e "USE spa_crud_tmdb; SHOW TABLES; SELECT COUNT(*) FROM movies_users; SELECT COUNT(*) FROM movies_user_favorite_movies;"
  ```

### Método 2: Importando pelo MySQL Workbench (Alternativo)

1. Abra o MySQL Workbench.
2. Conecte-se ao servidor:
   - Host: 127.0.0.1  
   - Porta: (verifique no docker-compose, normalmente 3306)  
   - Usuário: movies_user  
  3. (Opcional) Criar as tabelas via migrations do Laravel (caso não importe os arquivos .sql):
  ```bash
  docker-compose exec backend php artisan migrate
  ```
   Isso cria automaticamente as tabelas no schema spa_crud_tmdb conforme definido nas migrations.

4. Opção A (Data Import):
   - Menu: Server > Data Import.
   - Selecione "Import from Self-Contained File".
   - Aponte primeiro para `sql/movies_users.sql`.
   - Marque “Default Target Schema” = `spa_crud_tmdb`.
   - Clique em "Start Import".
   - Repita para `sql/movies_user_favorite_movies.sql`.
5. Opção B (Executando scripts manualmente):
   - Abra cada arquivo (`movies_users.sql` e `movies_user_favorite_movies.sql`) em uma aba (File > Open SQL Script).
   - Certifique-se de que o schema `spa_crud_tmdb` está selecionado (duplo clique no schema).
   - Execute (botão raio).
6. Confirme:
   ```sql
   USE spa_crud_tmdb;
   SHOW TABLES;
   SELECT COUNT(*) FROM movies_users;
   SELECT COUNT(*) FROM movies_user_favorite_movies;
   ```
7. Caso veja erros de chave duplicada, limpe antes:
   ```sql
   TRUNCATE TABLE movies_user_favorite_movies;
   TRUNCATE TABLE movies_users;
   ```

Pronto: banco preenchido para uso local.


---

## ● Indicação de onde está implementado o CRUD

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

## ● Instruções sobre como testar a aplicação
1.  **Acesso à Interface:**
  - Abra o navegador e acesse `http://localhost:5174`.
  - Use um dos logins de teste:
    - Email: `test@example.com` | Senha: `password`
    - Email: `jooj3068@gmail.com` | Senha: `senhaaaa123`
    - Email: `mu.540@example.com` | Senha: `senha123`
    - Email: `usuario@example.com` | Senha: `senha_forte123`

2.  **Testes Automatizados (Backend):**
    Para rodar a suíte de testes do Laravel (PHPUnit), execute o comando:
    ```bash
    docker-compose exec backend php artisan test
    ```

---

## ● Link para obter a chave da API do TMDB

Para que a aplicação funcione, é obrigatório o uso de uma chave de API do The Movie Database (TMDB).

-   **Link para o site oficial:** [The Movie Database (TMDB)](https://www.themoviedb.org)
-   **Instruções:** Crie uma conta, faça login e acesse a seção "Configurações" do seu perfil. No menu "API", você poderá gerar uma chave de API (v3 auth).

---

## ● Como subir o frontend separado

Se preferir rodar o frontend (Vue.js) de forma independente do Docker:

1.  **Navegue até o diretório do frontend:**
    ```bash
    cd frontend
    ```

2.  **Instale as dependências:**
    ```bash
    npm install
    ```

3.  **Execute a aplicação em modo de desenvolvimento:**
    ```bash
    npm run dev
    ```
    A aplicação estará disponível em `http://localhost:5173` (ou outra porta, caso a 5173 esteja ocupada).

**Importante:** Para que o frontend funcione, o backend (rodando via Docker) deve estar em execução, pois a interface faz chamadas para a API em `http://localhost:8080`.