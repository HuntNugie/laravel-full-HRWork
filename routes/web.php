<?php

use App\Livewire\Page\Auth\Login;
use Illuminate\Support\Facades\Route;
use App\Livewire\Page\Main\Dashboard\Dashboard;
Route::get('/', function () {
    return redirect()->route("login");
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});