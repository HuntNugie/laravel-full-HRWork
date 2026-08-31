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
use App\Livewire\Page\Main\Team\DetailTeam;
use App\Livewire\Page\Main\Team\Team;
use App\Livewire\Page\Main\User\User;

Route::get('/', function () {
    return redirect()->route("login");
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});


Route::middleware(['auth', 'isActive'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/my-profile', MyProfile::class)->name('my-profile');

    Route::prefix('divisi')->group(function () {
        Route::get('/', Divisi::class)->middleware('permission:view-divisi')->name('divisi.view');
        Route::get('/{divisi}/detail', DetailDivisi::class)->middleware('permission:show-divisi')->name('divisi.show');
    });

    Route::prefix('teams')->group(function () {
        Route::get('/', Team::class)->middleware('permission:view-team')->name('team.view');
        // 'permission:show-team'
        Route::get('/{team}/detail', DetailTeam::class)->middleware('permission:show-team')->name('team.show');
    });

    Route::prefix('employees')->group(function () {
        Route::get('/', Employee::class)->middleware('permission:view-employee')->name('employee.view');
        // untuk menambahkan employee
        Route::get('/create', CreateEmployee::class)->middleware('permission:create-employee')->name('employee.create');
        // untuk detail
        Route::get('/{employee}/detail', DetailEmployee::class)->middleware('permission:show-employee')->name('employee.show');

        // untuk contract

        Route::get('/{employee}/contract/create', CreateEmployeeContract::class)->middleware('permission:create-contract')->name('contract.create');
        // 'permission:show-contract'

    });

    Route::prefix('user')->group(function () {
        Route::get('/', User::class)->middleware('permission:view-user')->name('user.view');
    });

    Route::prefix('benefits')->group(function () {
        Route::get('/', Benefit::class)->middleware('permission:view-benefit')->name('benefit.view');
        Route::get('/{benefit}/detail', DetailBenefit::class)->middleware('permission:show-benefit')->name('benefit.show');
    });

    Route::prefix('positions')->group(function () {
        Route::get('/', Position::class)->middleware('permission:view-position')->name('position.view');
        Route::get('/{position}/detail', DetailPosition::class)->middleware("permission:show-position")->name('position.show');
    });
});
