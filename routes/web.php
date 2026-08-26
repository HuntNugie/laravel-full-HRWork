<?php

use App\Livewire\Components\Main\Employee\CreateEmployee;
use App\Livewire\Page\Auth\Login;
use App\Livewire\Page\Main\Base\MyProfile;
use App\Livewire\Page\Main\Benefit\Benefit;
use App\Livewire\Page\Main\Benefit\DetailBenefit;
use Illuminate\Support\Facades\Route;
use App\Livewire\Page\Main\Dashboard\Dashboard;
use App\Livewire\Page\Main\Divisi\DetailDivisi;
use App\Livewire\Page\Main\Divisi\Divisi;
use App\Livewire\Page\Main\Employee\Contract\CreateEmployeeContract;
use App\Livewire\Page\Main\Employee\DetailEmployee;
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

    Route::prefix('teams')->middleware(['permission:view-team', 'permission:show-team'])->group(function () {
        Route::get('/', Team::class)->name('team.view');
    });

    Route::prefix('employees')->middleware(['permission:view-employee', 'permission:show-employee'])->group(function () {
        Route::get('/', Employee::class)->name('employee.view');
        // untuk menambahkan employee
        Route::get('/create', CreateEmployee::class)->middleware('permission:create-employee')->name('employee.create');
        // untuk detail
        Route::get('/{employee}/detail', DetailEmployee::class)->name('employee.show');

        // untuk contract
        Route::prefix('contract')->middleware('permission:show-contract')->group(function () {
            Route::get('/{employee}/create', CreateEmployeeContract::class)->middleware('permission:create-contract')->name('contract.create');
        });
    });

    Route::prefix('benefits')->middleware(['permission:view-benefit', 'permission:show-benefit'])->group(function () {
        Route::get('/', Benefit::class)->name('benefit.view');
        Route::get('/{benefit}/detail', DetailBenefit::class)->name('benefit.show');
    });

    Route::prefix('positions')->middleware(['permission:view-position'])->group(function () {
        Route::get('/', Position::class)->name('position.view');
        Route::get('/{position}/detail', DetailPosition::class)->name('position.show');
    });
});
