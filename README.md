# RUBAC Service
Rule based acccess control service

## Set up and running

1. Copy .env.example to .env and set database values

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=rubac
DB_USERNAME=rubac_user
DB_PASSWORD=password
```

2. Build Docker images ```docker-compose build app```
3. Start Docker containers ```docker-compose up -d```
4. Install composer packages ```docker exec rubac-app composer install```
5. Generate application key ```docker exec rubac-app php artisan key:generate```
6. Run migrations and seed database ```docker exec rubac-app php artisan migrate```
```docker exec rubac-app php artisan db:seed```

Application is running on http://localhost:8000

## Workflow storage

Workflows are stored in JSON format, as part of configuration file.

Leverage config caching for faster read speeds.

Run command ```php artisan cache:config``` inside Docker container.

## RUBAC Service
RuleValidatorService is the core service, responsible for validating workflows and access control rules.

## RUBAC Middleware
Laravel Middleware that uses RUBAC Service and either allows request to continiue or returns an error message.

## Security of expression evaluation

The eval() language construct is very dangerous because it allows execution of arbitrary PHP code.
Which is major security flow on its own. However workflow configuration is stored in code base it self, without user provided inputs.