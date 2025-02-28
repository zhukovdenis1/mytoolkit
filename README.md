cd ..
composer create-project --prefer-dist laravel/laravel laravel_api

https://www.youtube.com/watch?v=J7MPj4FuEcM&t=310s - Установка JWT Auth. Aсcess(Login) token и Refresh token, token expired.

https://jwt-auth.readthedocs.io/en/develop/laravel-installation/

https://medium.com/@a3rxander/how-to-implement-jwt-authentication-in-laravel-11-26e6d7be5a41 - только не нужно регистрировать middleware->append

composer require tymon/jwt-auth

php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

php artisan jwt:secret

Edit UserModel - implements JWTSubject

php artisan make:middleware JwtMiddleware

Edit config/auth.php - add api guard

Добавляем в routes/api.php пути для авторизации

User::create(['name'=>'user', 'email'=>'test@test.ru', 'password' => Hash::make('123')])

php artisan make:controller AuthController

-------------------------

composer require --dev knuckleswtf/scribe

php artisan vendor:publish --tag=scribe-config

php artisan scribe:generate

---------------------------

4. Инструменты для модульной структуры
Если не хочется вручную создавать структуру, вы можете использовать пакет nwidart/laravel-modules:
composer require nwidart/laravel-modules

5. Создание модуля:

6. artisan module:make Blog

----------------------------

composer require laravel-lang/lang --dev

php artisan lang:publish

composer require fruitcake/laravel-cors - не поддерживается в ларавел 11

php artisan make:middleware HandleCors
