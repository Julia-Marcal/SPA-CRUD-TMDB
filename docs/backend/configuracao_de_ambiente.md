# Guia de Configuração do Ambiente Laravel

Este guia explica como configurar seu ambiente de desenvolvimento Laravel para funcionar tanto dentro do Docker quanto na sua máquina local.

## Configuração Atual

Seu projeto utiliza Docker com os seguintes serviços:
- **backend**: Aplicação Laravel
- **db**: Banco de dados MySQL 8.0
- **nginx_backend**: Servidor Nginx para Laravel (porta 8080)
- **nginx_frontend**: Servidor frontend (porta 5174)

## Configuração do Banco de Dados

O banco MySQL está acessível por:
- **Dentro do Docker**: `DB_HOST=db` (nome do serviço Docker)
- **Na máquina local**: `DB_HOST=127.0.0.1` (localhost, porta 3306)

Credenciais do banco:
- Banco: `movies`
- Usuário: `movies_user`
- Senha: `movies_password`

## Arquivos de Ambiente

### 1. `.env.docker` (para desenvolvimento com Docker)
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=movies
DB_USERNAME=movies_user
DB_PASSWORD=movies_password
```

### 2. `.env` (para desenvolvimento local)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=movies
DB_USERNAME=movies_user
DB_PASSWORD=movies_password
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:k5Mqa2o/AJL+l413k6SdzsJbt1y6LmKZ473KrQ+7v60=
APP_DEBUG=true
```

## Como Alternar Entre Ambientes

### Opção 1: Alternância Rápida (Recomendado)

**Para desenvolvimento com Docker:**
```bash
# Renomeie o .env atual para .env.docker
Rename-Item .env .env.docker

# Copie a configuração do Docker para .env
Copy-Item .env.docker .env
```

**Para desenvolvimento local:**
```bash
# Renomeie o .env atual para .env.docker
Rename-Item .env .env.docker

# Copie a configuração local para .env
Copy-Item .env.local .env
```
