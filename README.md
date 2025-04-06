# NeoTask API

API REST desenvolvida em Laravel para gerenciamento de tarefas.

## 📋 Pré-requisitos

### Para Ambiente Local
- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js (para compilação de assets)

### Para Docker
- Docker
- Docker Compose

## 🚀 Instalação

### 🖥️ Ambiente Local

1. Clone o repositório
```bash
git clone 
cd 
```

2. Instale as dependências do PHP
```bash
composer install
```

3. Copie o arquivo de ambiente
```bash
cp .env.example .env
```

4. Configure o arquivo `.env` com suas configurações locais
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

5. Gere a chave da aplicação
```bash
php artisan key:generate
```

6. Gere a chave JWT
```bash
php artisan jwt:secret
```

7. Execute as migrações
```bash
php artisan migrate
```

8. Inicie o servidor local
```bash
php artisan serve
```

A aplicação estará disponível em `http://localhost:8000`

### 🐳 Ambiente Docker

1. Clone o repositório
```bash
git clone 
cd 
```

2. Copie o arquivo de ambiente
```bash
cp .env.example .env
```

3. Configure o arquivo `.env` para o Docker
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

4. Construa e inicie os containers
```bash
docker-compose build
docker-compose up -d
```

5. Gere a chave JWT dentro do container
```bash
docker-compose exec app php artisan jwt:secret
```

A aplicação estará disponível em `http://localhost:8000`

## 📦 Estrutura Docker

O projeto utiliza três containers Docker:
- **app**: Aplicação Laravel (PHP 8.2-FPM)
- **nginx**: Servidor Web (Nginx Alpine)
- **db**: Banco de Dados (MySQL 8.0)

### Portas Expostas
- Aplicação: 8000
- MySQL: 3306

### Credenciais do Banco (Docker)
- Database: `laravel`
- Usuário: `laravel`
- Senha: `secret`

## 🛠️ Comandos Úteis

### Ambiente Local
```bash
# Executar migrações
php artisan migrate

# Criar um novo controller
php artisan make:controller NomeController

# Limpar cache
php artisan cache:clear

# Executar testes
php artisan test
```

### Ambiente Docker
```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Visualizar logs
docker-compose logs -f

# Acessar o shell da aplicação
docker-compose exec app bash

# Executar comandos Artisan
docker-compose exec app php artisan <comando>

# Reconstruir containers (após alterações no Dockerfile)
docker-compose build --no-cache
```

## 🔍 Monitoramento

### Logs Docker
```bash
# Todos os containers
docker-compose logs -f

# Container específico
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

## 🚨 Troubleshooting

### Problemas Comuns

1. **Erro de conexão com o banco**
   - Verifique se as credenciais no `.env` estão corretas
   - Para Docker: aguarde alguns segundos após iniciar os containers

2. **Permissões de arquivos**
   - Execute: `chmod -R 777 storage bootstrap/cache`

3. **Container não inicia**
   - Verifique se as portas não estão em uso
   - Logs: `docker-compose logs -f`

4. **Erro: Secret is not set (JWT)**
   - Este erro ocorre quando a chave JWT não foi gerada
   - Para ambiente local: `php artisan jwt:secret`
   - Para Docker: `docker-compose exec app php artisan jwt:secret`
   - Após gerar a chave, reinicie o container: `docker-compose restart app`

