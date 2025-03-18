<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Console\Http\Controllers\User\ConsoleController;
use App\Modules\FileStore\Http\Controllers\User\CacheController;

Route::get('/console/{command}', [ConsoleController::class, 'runCommand']);
Route::get('/uploads/public/{id}_{name}', [CacheController::class, 'public']);
Route::get('/uploads/private/{id}_{name}', [CacheController::class, 'private']);

Route::get('/{any}', function () {
    return view('main');
})->where('any', '.*');
