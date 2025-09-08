<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ItemController;



// 会員登録フォーム表示
Route::get('/register', [RegisterController::class, 'register'])->name('register');

// 登録処理
Route::post('/register', [RegisterController::class, 'create'])->name('register.post');

//login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login');

//認証が必要な画面をこの中に（後で追加していく）
Route::middleware('auth')->group(function () {

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',       [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/items/{item}/like', [ItemController::class, 'toggleLike'])->name('items.like');
    Route::post('/items/{item}/comments', [ItemController::class, 'storeComment'])
        ->name('items.comments.store');
        
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items',        [ItemController::class, 'store'])->name('items.store');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    
});

//商品一覧
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
//検索機能
Route::get('/search', [ItemController::class, 'search'])->name('items.search');
//商品詳細
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

