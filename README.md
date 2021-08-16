[![StyleCI](https://styleci.io/repos/55775729/shield?branch=master)](https://styleci.io/repos/55775729)

Install the cms with composer
```json
 composer require "vis/builder_lara_5":"3.*"
```

Install cms
```json
   php artisan admin:install
```

Generate a password for admin
```json
   php artisan admin:generatePassword
```

Yoo can publish vendor
```json   
   php artisan vendor:publish --tag=public --force --provider="Vis\Builder\BuilderServiceProvider"
```
