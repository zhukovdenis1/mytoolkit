<?php

use App\Modules\Auth\Http\Controllers\Shared\AuthController;
use App\Modules\Note\Http\Controllers\User\NoteCategoryController;
use App\Modules\Note\Http\Controllers\User\NoteController;
//use App\Modules\Product\Http\Controllers\User\ProductController;
use App\Modules\Note\Http\Controllers\User\NoteFileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;

Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::middleware([JwtMiddleware::class])->group(function () {

    Route::get('me', [AuthController::class, 'me']);
    Route::get('users', [AuthController::class, 'users']);



    Route::prefix('notes/{note}/files')->group(function () {
        Route::post('/', [NoteFileController::class, 'store']);
        Route::delete('/', [NoteFileController::class, 'destroy']);
        Route::post('/tg', [NoteFileController::class, 'tg']);
    });

    Route::patch('notes/{note}/add-categories', [NoteController::class, 'addCategories']);

    Route::prefix('notes/categories')->group(function () {
        Route::get('/', [NoteCategoryController::class, 'index']);
        Route::get('/all', [NoteCategoryController::class, 'all']);
        Route::get('/tree', [NoteCategoryController::class, 'tree']);
        Route::post('/', [NoteCategoryController::class, 'store']); // Создание категории
        Route::get('{category}', [NoteCategoryController::class, 'show'])->where('category', '[0-9]+'); // Получение категории по ID
        Route::put('{category}', [NoteCategoryController::class, 'update']); // Обновление категории
        Route::delete('{category}', [NoteCategoryController::class, 'destroy']); // Удаление категории
    });

    Route::prefix('notes')->group(function () {
        Route::get('/', [NoteController::class, 'index'])->name('notes.search');
        Route::get('/get-dropdown', [NoteController::class, 'getDropDown']);
        Route::get('/tree', [NoteController::class, 'tree']);
        Route::post('/', [NoteController::class, 'store'])->name('notes.store');
        Route::get('{note}', [NoteController::class, 'show'])->where('note', '[0-9]+');
        Route::put('{note}/edit-content', [NoteController::class, 'updateContent']);
        Route::put('{note}', [NoteController::class, 'update']);
        Route::delete('{note}', [NoteController::class, 'destroy']);
    });
});

//Route::get('products', [ProductController::class, 'index']);
//Route::post('products', [ProductController::class, 'store']);
//Route::get('products/{id}', [ProductController::class, 'show']);
//Route::put('products/update/{id}', [ProductController::class, 'update']);
//Route::delete('products/delete/{id}', [ProductController::class, 'destroy']);
