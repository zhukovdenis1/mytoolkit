<?php

use App\Modules\Main\Http\Controllers\Shared\MainController;
use App\Modules\Note\Http\Controllers\Shared\NoteController;
use App\Modules\Util\Http\Controllers\Shared\UtilController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use App\Modules\Console\Http\Controllers\User\ConsoleController;
use App\Modules\FileStorage\Http\Controllers\User\FileStorageController;

Route::middleware([\App\Http\Middleware\WebAuthMiddleware::class])->group(function () {
    Route::get('', [MainController::class, 'index'])->name('home');
    Route::get('a-{noteId}', [NoteController::class, 'detail'])/*->name('note.detail')*/;
    Route::any('cp', [MainController::class, 'copyPaste']);
    Route::any('myip', [MainController::class, 'myIp']);
});

Route::any('/console/{category}/{command}', [ConsoleController::class, 'runCommand'])
    ->withoutMiddleware(VerifyCsrfToken::class);
Route::any('/console/{command}', [ConsoleController::class, 'runCommand'])
    ->withoutMiddleware(VerifyCsrfToken::class);

Route::middleware([\App\Http\Middleware\WebCheckAuthMiddleware::class])->group(function () {
    Route::get('/uploads/storage/{user_id}/{module_name}/{module_id}/{file}_{file_name}.{file_ext}', [FileStorageController::class, 'get']);
    //Route::delete('/uploads/storage/{user_id}/{module_name}/{module_id}/{file}_{file_name}.{file_ext}', [FileStorageController::class, 'delete'])
    //    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::any('utils/{utilName?}', [UtilController::class, 'index'])->name('util');
});

Route::get('/{any}', function () {
    return view('main');
})->where('any', '.*');



