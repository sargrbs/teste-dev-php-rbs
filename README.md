
## Teste para Desenvolvedor PHP/Laravel - Rubens Braga


## Tecnologias Utilizadas
    - PHP 8.2
    - Laravel 10
    - Docker & Docker Compose
    - MySQL 8.0
    - Redis (cache)
    - Laravel Octane com Swoole
    - Laravel Sanctum para autenticação
    - Repository Pattern
    - Clean Architecture

## 📋 Pré-requisitos

    - Docker & Docker Compose instalados
    - Git
    - Porta 8000 disponível para a API
    - Porta 3306 disponível para MySQL
    - Porta 6379 disponível para Redis

## 🚀 Iniciando o Projeto

    1. Clone o repositório:
        git clone <url-do-repositorio>
        cd <nome-do-projeto>

    2. Copie o arquivo de ambiente:    
        cp .env.example .env

    3. Configure as variáveis de ambiente no arquivo .env:
        APP_NAME=SupplierAPI
        APP_ENV=local
        APP_DEBUG=true
        APP_URL=http://localhost:8000

        DB_CONNECTION=mysql
        DB_HOST=db
        DB_PORT=3306
        DB_DATABASE=supplier_db
        DB_USERNAME=supplier
        DB_PASSWORD=password

        CACHE_DRIVER=redis
        REDIS_HOST=redis
        REDIS_PASSWORD=null
        REDIS_PORT=6379
        REDIS_CLIENT=predis

        OCTANE_SERVER=swoole

    4. Inicie os containers Docker:
        docker-compose up -d --build

    5. Entre no container da aplicação:
        docker-compose exec app bash

    6. Configure o projeto:    
        php artisan key:generate
        php artisan migrate

## 📚 Estrutura do projeto

    app/
    ├── Http/
    │   ├── Controllers/
    |   |   ├── Auth/
    |   |   |   └── AuthController.php
    │   │   └── SupplierController.php
    │   └── Requests/
    │       └── SupplierRequest.php
    ├── Manager/
    │   └── AbstractManager.php
    |   └── SupplierManager.php
    ├── Models/
    │   └── Supplier.php
    |   └── User.php
    ├── Repositories/
    │   ├── Contracts/
    │   │   └── SupplierRepositoryInterface.php
    │   └── SupplierRepository.php
    ├── Rules/
    |    └── DocumentValidation.php
    ├──Services/
    |    └── BrasilApiService.php
    └── Utils/
        └── Constants.php    

## 🔄 Endpoints da API      

    1. Autenticação
        POST /api/register
        POST /api/login
        POST /api/logout
     
    2. Fornecedores  
        GET    /api/suppliers          - Lista fornecedores | Paginação
        POST   /api/suppliers         - Cria fornecedor
        GET    /api/suppliers/{id}    - Busca fornecedor
        PUT    /api/suppliers/{id}    - Atualiza fornecedor
        DELETE /api/suppliers/{id}    - Remove fornecedor 

        EXTRA:
        POST /api/suppliers/find-cnpj - Busca dados no endpoint https://brasilapi.com.br

    3. Health Check     
        GET /api/health-check

## 📝 Exemplos de Uso

    1. Registro de Usuário
        curl -X POST http://localhost:8000/api/register \
        -H "Content-Type: application/json" \
        -d '{
            "name": "Test User",
            "email": "test@example.com",
            "password": "password",
            "password_confirmation": "password"
        }'

    2. Login    
        curl -X POST http://localhost:8000/api/login \
        -H "Content-Type: application/json" \
        -d '{
            "email": "test@example.com",
            "password": "password"
        }'

    3. Criar Fornecedor
        curl -X POST http://localhost:8000/api/suppliers \
        -H "Authorization: Bearer {token}" \
        -H "Content-Type: application/json" \
        -d '{
            "name": "Fornecedor Teste",
            "document": "12345678901234",
            "document_type": "CNPJ",
            "email": "fornecedor@teste.com",
            "phone": "11999999999",
            "street": "Rua Teste",
            "number": "123",
            "neighborhood": "Centro",
            "city": "São Paulo",
            "state": "SP",
            "zip_code": "12345678"
        }'

    4. Buscar Fornecedor
        curl -X GET http://localhost:8000/api/suppliers/1 \
        -H "Authorization: Bearer {token}"

    5. Listar Fornecedores com Filtro
        curl -X GET "http://localhost:8000/api/suppliers?search=termo&page=1" \
        -H "Authorization: Bearer {token}"
    
## 🛠️ Comandos Úteis

    Docker:

        # Iniciar containers
        docker-compose up -d

        # Parar containers
        docker-compose down

        # Logs dos containers
        docker-compose logs -f

        # Acessar container da aplicação
        docker-compose exec app bash

        # Acessar MySQL
        docker-compose exec db mysql -u supplier -p

    Artisan:
        # Limpar caches
        php artisan config:clear
        php artisan cache:clear
        php artisan route:clear

        # Rodar migrations
        php artisan migrate

        # Rodar testes
        php artisan test
    
    Testes:
        php artisan test

## 📦 Recursos Implementados

    1. CRUD de fornecedores
    2. Autenticação com Sanctum
    3. Cache com Redis
    4. Validação de CPF/CNPJ
    5. Integração com BrasilAPI
    6. Repository Pattern
    7. Soft Delete
    8. Busca por documento/nome
    9. Paginação
    10. Testes automatizados
    11. Docker
    12. Swoole

        


         
    
    




