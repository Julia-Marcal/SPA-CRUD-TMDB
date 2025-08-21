# Cartão de Referência Rápida

## 🚀 Início Rápido
```bash
# Iniciar serviços Docker
docker-compose up -d

# Alternar para desenvolvimento local
switch-to-local.bat

# Alternar para desenvolvimento com Docker  
switch-to-docker.bat
```

## 🔄 Alternância de Ambiente

| Necessidade Atual | Execute Isto | Resultado |
|-------------------|--------------|-----------|
| Executar `php artisan migrate` localmente | `switch-to-local.bat` | DB_HOST=127.0.0.1 |
| Executar `docker-compose exec backend php artisan migrate` | `switch-to-docker.bat` | DB_HOST=db |

## 📋 Comandos Essenciais

### Ambiente Docker
```bash
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan make:controller MovieController
docker-compose exec backend php artisan serve
```

### Ambiente Local  
```bash
php artisan migrate
php artisan make:controller MovieController
php artisan serve
```
