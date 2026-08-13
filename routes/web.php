<?php

use App\Livewire\Page\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route("login");
});

Route::middleware('guest')->group(function () {
    Route::get('/login',Login::class)->name('login');
});
