[![StyleCI](https://styleci.io/repos/55775729/shield?branch=master)](https://styleci.io/repos/55775729)

Подкючаем 
```json
 composer require "vis/builder_lara_5":"1.*"
```
Подкючаем для laravel 6.*
```json
 composer require "vis/builder_lara_5":"2.*"
```
Добавляем в файле app.php в блок providers
```php
  Vis\Builder\BuilderServiceProvider::class,
```
Добавляем в файле app.php в блок alias
```php
  'Jarboe' => Vis\Builder\Facades\Jarboe::class,
```

Инсталим админку
```json
   php artisan admin:install
```
Генерируем пароль для админа
```json
   php artisan admin:generatePassword
```

Если нужно обновить css и js, то
```json   
   php artisan vendor:publish --tag=public --force --provider="Vis\Builder\BuilderServiceProvider"
```
