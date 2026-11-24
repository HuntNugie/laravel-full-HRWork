<?php

use App\Http\Controllers\PrintContractEmployeeController;
use App\Livewire\Components\Main\Employee\CreateEmployee;
use App\Livewire\Page\Auth\Login;
use App\Livewire\Page\Main\Base\MyProfile;
use App\Livewire\Page\Main\Benefit\Benefit;
use App\Livewire\Page\Main\Benefit\DetailBenefit;
use App\Livewire\Page\Main\Contract\Contract;
use App\Livewire\Page\Main\Dashboard\Dashboard;
use App\Livewire\Page\Main\Employee\Contract\CreateEmployeeContract;
use App\Livewire\Page\Main\Employee\Contract\DetailEmployeeContract;
use App\Livewire\Page\Main\Employee\Contract\EditEmployeeContract;
use App\Livewire\Page\Main\Employee\DetailEmployee;
use App\Livewire\Page\Main\Employee\EditEmployee;
use App\Livewire\Page\Main\Employee\Employee;
use App\Livewire\Page\Main\Leave\DetailLeaveType;
use App\Livewire\Page\Main\Leave\LeaveType;
use App\Livewire\Page\Main\Position\DetailPosition;
use App\Livewire\Page\Main\Position\Position;
use App\Livewire\Page\Main\User\DetailRolePermission;
use App\Livewire\Page\Main\User\DetailUser;
use App\Livewire\Page\Main\User\User;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));
Route::middleware('guest')->get('/login', Login::class)->name('login');

Route::middleware(['auth', 'isActive', 'role:administrator|hr'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/my-profile', MyProfile::class)->name('my-profile');

    Route::middleware('role:administrator')->prefix('user')->group(function () {
        Route::get('/', User::class)->middleware('permission:view-user')->name('user.view');
        Route::get('/{user}/detail', DetailUser::class)->middleware('permission:show-user')->name('user.show');
        Route::get('/{user}/role/detail', DetailRolePermission::class)->middleware('permission:manage-hr-access')->name('user.role.show');
    });

    Route::middleware('role:hr')->group(function () {
        Route::prefix('employees')->group(function () {
            Route::get('/', Employee::class)->middleware('permission:view-employee')->name('employee.view');
            Route::get('/create', CreateEmployee::class)->middleware('permission:create-employee')->name('employee.create');
            Route::get('/{employee}/edit', EditEmployee::class)->middleware('permission:update-employee')->name('employee.edit');
            Route::get('/{employee}/detail', DetailEmployee::class)->middleware('permission:show-employee')->name('employee.show');
            Route::get('/{employee}/contract/create', CreateEmployeeContract::class)->middleware('permission:create-contract')->name('contract.create');
            Route::get('/{employee}/contract/{contract}/detail', DetailEmployeeContract::class)->middleware('permission:show-contract')->name('contract.show');
            Route::get('/{employee}/contract/edit', EditEmployeeContract::class)->middleware('permission:update-contract')->name('contract.edit');
        });
        Route::get('/contract', Contract::class)->middleware('permission:view-contract')->name('contract.view');
        Route::get('/print/employee/{employee}/contract/{contract}', PrintContractEmployeeController::class)->middleware('permission:download-employee-contract')->name('print.contract.employee');
        Route::prefix('positions')->group(function () {
            Route::get('/', Position::class)->middleware('permission:view-position')->name('position.view');
            Route::get('/{position}/detail', DetailPosition::class)->middleware('permission:show-position')->name('position.show');
        });
        Route::prefix('benefits')->group(function () {
            Route::get('/', Benefit::class)->middleware('permission:view-benefit')->name('benefit.view');
            Route::get('/{benefit}/detail', DetailBenefit::class)->middleware('permission:show-benefit')->name('benefit.show');
        });
        Route::prefix('leave')->group(function () {
            Route::get('/type', LeaveType::class)->middleware('permission:view-type-leave')->name('leave.type.view');
            Route::get('/type/{leavetype}', DetailLeaveType::class)->middleware('permission:show-type-leave')->name('leave.type.show');
        });
    });
});
