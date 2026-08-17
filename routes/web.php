<?php

use App\Livewire\Page\Auth\Login;
use App\Livewire\Page\Main\Base\MyProfile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Page\Main\Dashboard\Dashboard;
use App\Livewire\Page\Main\Divisi\DetailDivisi;
use App\Livewire\Page\Main\Divisi\Divisi;

Route::get('/', function () {
    return redirect()->route("login");
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});


Route::middleware(['auth','isActive'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/my-profile', MyProfile::class)->name('my-profile');

    Route::prefix('divisi')->group(function(){
        Route::middleware(['permission:view-divisi','permission:show-divisi'])->group(function(){
            Route::get('/view',Divisi::class)->name('divisi.view');
            Route::get('/{divisi}/detail',DetailDivisi::class)->name('divisi.name');
        });
    });
});