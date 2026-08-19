<?php

use App\Livewire\Page\Auth\Login;
use App\Livewire\Page\Main\Base\MyProfile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Page\Main\Dashboard\Dashboard;
use App\Livewire\Page\Main\Divisi\DetailDivisi;
use App\Livewire\Page\Main\Divisi\Divisi;
use App\Livewire\Page\Main\Employee\Employee;
use App\Livewire\Page\Main\Position\DetailPosition;
use App\Livewire\Page\Main\Position\Position;
use App\Livewire\Page\Main\Team\Team;

Route::get('/', function () {
    return redirect()->route("login");
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});


Route::middleware(['auth', 'isActive'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/my-profile', MyProfile::class)->name('my-profile');

    Route::prefix('divisions')->middleware(['permission:view-divisi', 'permission:show-divisi'])->group(function () {
        Route::get('/', Divisi::class)->name('divisi.view');
        Route::get('/{divisi}/detail', DetailDivisi::class)->name('divisi.name');
    });

    Route::prefix('teams')->middleware(['permission:view-team','permission:show-team'])->group(function(){
        Route::get('/',Team::class)->name('team.view');
    });

    Route::prefix('employees')->middleware(['permission:view-employee'])->group(function(){
        Route::get('/',Employee::class)->name('employee.view');
    });

    Route::prefix('positions')->middleware(['permission:view-position'])->group(function(){
        Route::get('/',Position::class)->name('position.view');
        Route::get('/{position}/detail',DetailPosition::class)->name('position.show');
    });


});