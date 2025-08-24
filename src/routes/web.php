<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MyPageController;



// 会員登録フォーム表示
Route::get('/register', [RegisterController::class, 'register'])->name('register');

// 登録処理
Route::post('/register', [RegisterController::class, 'create'])->name('register.post');

//login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login');

//認証が必要な画面をこの中に（後で追加していく）
Route::middleware('auth')->group(function () {

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
   
});


