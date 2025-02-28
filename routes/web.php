<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Console\Http\Controllers\User\ConsoleController;

Route::get('/console/{command}', [ConsoleController::class, 'runCommand']);

Route::get('/{any}', function () {
    return view('main');
})->where('any', '.*');
