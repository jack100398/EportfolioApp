# e-portfolio-app

## Pre-request skill

-   [Eloquent](https://laravel.com/docs/8.x/eloquent)
-   [Sail](https://laravel.com/docs/8.x/sail)
-   [Artisan](https://laravel.com/docs/8.x/artisan#introduction)
-   [Laravel](https://laravel.com/)
-   [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper)
-   [Swagger](https://blog.quickadminpanel.com/laravel-api-documentation-with-openapiswagger/)

## System Requirement

-   PHP 8.0 or newer
-   composer 2 or newer
-   Docker and docker-compose

## How to startup this project

1. Clone the repository and enter the project root
2. Use composer to install necessary package
3. Run ./vendor/bin/sail up to start your project
4. More information please check the document of laravel: https://laravel.com/docs/8.x/installation

## How to debug this project

1. Install the extension `PHP Debug`
2. `SAIL_DEBUG` needs to be true in `.env`
3. I have created a launch.json file in the .vscode file, so you just press `F5` to start debugging mode

## Note

### Cheatsheet

https://learnku.com/docs/laravel-cheatsheet/8.x

## Useful command

### Available commands for the "make" namespace

```
php artisan list make
```

### Create controller with CRUD

```sh
php artisan make:controller SomeController --api
```

### Create controller with CRUD and model

```sh
php artisan make:controller PhotoController --api --model=Photo
```

## Migration

```sh
php artisan make:migration create_some_table
```

### Drop database and run all migration again after add a new migration

```sh
artisan migrate:fresh
```

### Seed data during migration

```sh
artisan migrate:fresh --seed
```

## Testing

```sh
artisan test
```

-   [PHPUnit 入門與實戰](https://jaceju-books.gitbooks.io/phpunit-in-action/content/index.html)
-   [Reference](https://github.com/framgia/laravel-test-guideline)

### Coverage

```sh
XDEBUG_MODE=coverage phpunit --coverage-html ./report
```

## Lint

-   php-cs-fixer

## Code Quality Analyzer

-   [PHP Insights](https://phpinsights.com/)
-   Use the following code to analyze

```sh
 php artisan insights
```

## RECTOR

```sh
./vendor/bin/rector process app
```

## Before publish

https://laravel.com/docs/8.x/deployment#optimization

## Note

1. [Soft Deleting and Permanently Deleting](https://laravel.com/docs/8.x/eloquent#soft-deleting)
