<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/phone', [HomeController::class, 'index'])->name('phone');
Route::get('/phone', [HomeController::class, 'index'])->name('phone');
Auth::routes();

Route::get('/home', [HomeController::class, 'showHome'])->name('home');

Route::post('/verify', [HomeController::class, 'verify'])->name('verify_phone_otp');
Route::get('/verify', [HomeController::class, 'showVerifyPhoneOtp'])->name('show_verify_phone_otp');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');
