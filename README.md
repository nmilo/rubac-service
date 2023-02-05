# RuBAC Service
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
6. Run migrations ```docker exec rubac-app php artisan migrate```
7. Seed the database ```docker exec rubac-app php artisan db:seed```

Application is running on http://localhost:8000

## Workflow storage

Workflows are stored in JSON format, as part of configuration file.
However workflows can use another storage driver, by implementing WorkflowRepositoryInterface.

You can optionaly leverage config caching for faster read speeds.
By running ```php artisan cache:config``` inside Docker container.

## RUBAC Service
RubacValidatorService is the core service, responsible for validating workflows and access control rules.
Core method ```validate(User $user, BaseRequest $request, $workflows)``` with takes User, Request and Workflow objects.

## RUBAC Middleware
Laravel Middleware that uses RUBAC Service and either allows request to continue or returns an error message.
To enable that middleware on route add ```rubac``` to middleware group.

## Expression Evaluator

Implementation of Interpreter Design Pattern. This class is responsible for parsing and evaluating expressions.
As well as managing local parameters as defined in rule definition.

## Default users and manual testing
By default there are two users in database, super_admin@example.com and admin@example.com that have associated roles.
You can login with post request to api/login, with email and password. Default password is password. You will get an API token, which you can later use for other requests. Token is used as Authorization header.

Defined rotues /api/admin/settings and /api/admin/users can be used for rule testing.

## Security of expression evaluation

The eval() language construct is very dangerous because it allows execution of arbitrary PHP code.
Which is major security flow on its own. However workflow configuration is stored in code base it self, without user provided inputs.

## Tests
Run tests with ```php artisan test```
Tests RubacService, WorkflowRepository and ExpressionEvaluator

