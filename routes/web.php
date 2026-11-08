<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// ===== Tamu =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

    Route::get('/forgot-password', [PasswordResetController::class, 'showRequest'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Barang & profil bisa dilihat tanpa login
Route::get('/items/{item}', [ItemController::class, 'show'])->whereNumber('item')->name('items.show');
Route::get('/users/{user}', [ProfileController::class, 'show'])->whereNumber('user')->name('profile.show');

// ===== Pengguna login =====
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Barang
    Route::get('/my-items', [ItemController::class, 'mine'])->name('items.mine');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    // Penawaran
    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::get('/items/{item}/offer', [OfferController::class, 'create'])->name('offers.create');
    Route::post('/items/{item}/offer', [OfferController::class, 'store'])->name('offers.store');
    Route::post('/offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
    Route::post('/offers/{offer}/reject', [OfferController::class, 'reject'])->name('offers.reject');
    Route::post('/offers/{offer}/cancel', [OfferController::class, 'cancel'])->name('offers.cancel');
    Route::post('/offers/{offer}/complete', [OfferController::class, 'complete'])->name('offers.complete');
    Route::post('/offers/{offer}/cancel-deal', [OfferController::class, 'cancelDeal'])->name('offers.cancelDeal');

    Route::post('/offers/{offer}/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Favorit
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'toggle'])->middleware('throttle:60,1')->name('favorites.toggle');

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{offer}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/{offer}/messages', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/{offer}/messages', [ChatController::class, 'send'])->middleware('throttle:30,1')->name('chat.send');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/open', [NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::get('/api/unread-counts', [NotificationController::class, 'counts'])->name('api.counts');

    // Profil & pengaturan
    Route::get('/profile', [ProfileController::class, 'me'])->name('profile');
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings');
    Route::put('/settings', [ProfileController::class, 'update'])->name('settings.update');
    Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('settings.password');
});
