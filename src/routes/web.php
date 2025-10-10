<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\EmailConfirmController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\VerificationController;


// 会員登録フォーム表示
Route::get('/register', [RegisterController::class, 'register'])->name('register');

// 登録処理
Route::post('/register', [RegisterController::class, 'create'])->name('register.post');

//login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login');

//商品一覧
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
//検索機能
Route::get('/search', [ItemController::class, 'search'])->name('items.search');


// 1) 誘導画面：「認証はこちらから」ボタン
Route::get('/email/verify', [EmailConfirmController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

// 2) 中間画面：「認証する」ボタン
Route::get('/email/verify/confirm', [EmailConfirmController::class, 'show'])
    ->middleware('auth')
    ->name('verification.confirm');

Route::get('/verification/check', [VerificationController::class, 'check'])
    ->name('verification.check')
    ->middleware('auth');

// 3) 「認証する」押下 → 署名付きURLを作り、本家 verify に飛ばす
Route::post('/email/verify/perform', [EmailConfirmController::class, 'perform'])
    ->middleware(['auth','throttle:6,1'])
    ->name('verification.perform');

// 4) 本家 verify（Laravel標準）: 認証完了後に商品一覧へ
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('items.index');
})->middleware(['auth','signed'])->name('verification.verify');

// 5) 認証メール「再送」… コントローラ不要（クロージャ）
Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return back();
    }
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');




//認証が必要な画面をこの中に（後で追加していく）
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ← これを items/{item} より“前”に置く
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');

    Route::post('/items/{item}/like', [ItemController::class, 'toggleLike'])->name('items.like');
    Route::post('/items/{item}/comments', [ItemController::class, 'storeComment'])->name('items.comments.store');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/items/{item}/buy',  [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/items/{item}/buy', [PurchaseController::class, 'store'])->name('purchase.store');

    Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/cancel',  [PurchaseController::class, 'cancel'])->name('purchase.cancel');

    Route::post('/purchase/checkout/{item}', [PurchaseController::class, 'checkout'])
        ->where('item', '[0-9]+')
        ->name('purchase.checkout');

    Route::get('/purchase/{item}', [PurchaseController::class, 'create'])
        ->where('item', '[0-9]+')
        ->name('purchase.create');

    Route::get('/purchase/{item}/address',  [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::put('/purchase/{item}/address',  [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});

// ★ 商品詳細は“最後”に置く + 数値IDだけ許可
Route::get('/items/{item}', [ItemController::class, 'show'])
    ->whereNumber('item')
    ->name('items.show');



