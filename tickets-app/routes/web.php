<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PqrsController;
use Illuminate\Support\Facades\Route;

// ── Público ──────────────────────────────────────────────────────────────────
Route::get('/',            [EventController::class, 'index'])->name('home');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

// ── Auth (solo para no autenticados) ─────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get ('/login',    [LoginController::class,    'showForm'])->name('login');
    Route::post('/login',    [LoginController::class,    'login'])->name('login.post');
    Route::get ('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    Route::get ('/auth/google',          [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get ('/auth/google/callback', [GoogleController::class, 'callback']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Área privada (requiere token de API) ──────────────────────────────────────
Route::middleware('api.auth')->group(function () {

    // Dashboard → redirige a entradas
    Route::get('/dashboard', fn () => redirect()->route('orders.index'))->name('dashboard');

    // Perfil
    Route::get ('/profile',       [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile',       [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo');

    // Mis entradas
    Route::get('/my-tickets',      [OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-tickets/{id}', [OrderController::class, 'show'])->name('orders.show');

    // Checkout
    Route::get ('/events/{eventId}/showtimes/{showtimeId}/seats',
                [OrderController::class, 'seats'])->name('checkout.seats');
    Route::post('/checkout/reserve', [OrderController::class, 'reserve'])->name('checkout.reserve');
    Route::get ('/checkout/confirm', [OrderController::class, 'confirmPage'])->name('checkout.confirm');
    Route::post('/checkout/pay',     [OrderController::class, 'pay'])->name('checkout.pay');
    Route::get ('/checkout/success/{orderId}',
                [OrderController::class, 'success'])->name('checkout.success');

    // Favoritos
    Route::get ('/favorites',          [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{eventId}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // PQRS / Soporte
    Route::get ('/support', [PqrsController::class, 'index'])->name('pqrs.index');
    Route::post('/support', [PqrsController::class, 'store'])->name('pqrs.store');
});
