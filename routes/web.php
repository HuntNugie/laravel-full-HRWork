<?php

use App\Http\Controllers\PrintContractEmployeeController;
use App\Http\Controllers\PrintPayrollPaymentController;
use App\Http\Controllers\PrintPayrollSlipController;
use App\Http\Controllers\PrintPayrollSummaryController;
use App\Http\Controllers\PrintWarningLetterController;
use App\Livewire\Components\Main\Employee\CreateEmployee;
use App\Livewire\Page\Auth\Login;
use App\Livewire\Page\Main\Absence\Absence;
use App\Livewire\Page\Main\Attendances\ApprovedAbsence;
use App\Livewire\Page\Main\Attendances\Attendances;
use App\Livewire\Page\Main\Attendances\DailyStatus;
use App\Livewire\Page\Main\Attendances\HistoryAttendanceManage;
use App\Livewire\Page\Main\Attendances\HistoryAttendances;
use App\Livewire\Page\Main\Attendances\MonitorAttendance;
use App\Livewire\Page\Main\Base\MyProfile;
use App\Livewire\Page\Main\Benefit\Benefit;
use App\Livewire\Page\Main\Benefit\DetailBenefit;
use App\Livewire\Page\Main\Contract\Contract;
use App\Livewire\Page\Main\WorkManagement\WorkManagement;
use Illuminate\Support\Facades\Route;
use App\Livewire\Page\Main\Dashboard\Dashboard;

Route::get('/', fn () => redirect()->route('login'));
Route::middleware('guest')->group(function () { Route::get('/login', Login::class)->name('login'); });
Route::middleware(['auth', 'isActive'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/work-management', WorkManagement::class)->name('work-management.view');
});
