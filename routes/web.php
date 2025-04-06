<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Console\Http\Controllers\User\ConsoleController;
use App\Modules\FileStorage\Http\Controllers\User\FileStorageController;

Route::get('/console/{command}', [ConsoleController::class, 'runCommand']);

$domain = request()->getHost();

if ($domain === 'deshevyi.loc') {
    require __DIR__ . '/shop.php';
} else {
    Route::middleware([\App\Http\Middleware\WebAuthMiddleware::class])->group(function () {
        Route::get('/uploads/storage/{user_id}/{module_name}/{module_id}/{file}_{file_name}.{file_ext}', [FileStorageController::class, 'get']);
        //Route::delete('/uploads/storage/{user_id}/{module_name}/{module_id}/{file}_{file_name}.{file_ext}', [FileStorageController::class, 'delete'])
        //    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    });


    Route::get('/{any}', function () {
        return view('main');
    })->where('any', '.*');
}


