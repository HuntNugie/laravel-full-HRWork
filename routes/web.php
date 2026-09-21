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
use Illuminate\Support\Facades\Route;
use App\Livewire\Page\Main\Dashboard\Dashboard;
use App\Livewire\Page\Main\Dicipline\DetailWarningLetter;
use App\Livewire\Page\Main\Dicipline\LateDiciplineRule;
use App\Livewire\Page\Main\Dicipline\UnpresentDisciplineRule;
use App\Livewire\Page\Main\Dicipline\WarningLetter;
use App\Livewire\Page\Main\Discipline\LateDiciplineRule as DisciplineLateDiciplineRule;
use App\Livewire\Page\Main\Divisi\DetailDivisi;
use App\Livewire\Page\Main\Divisi\Divisi;
use App\Livewire\Page\Main\Employee\Contract\CreateEmployeeContract;
use App\Livewire\Page\Main\Employee\Contract\DetailEmployeeContract;
use App\Livewire\Page\Main\Employee\Contract\EditEmployeeContract;
use App\Livewire\Page\Main\Employee\DetailEmployee;
use App\Livewire\Page\Main\Employee\DetailMyContract;
use App\Livewire\Page\Main\Employee\EditEmployee;
use App\Livewire\Page\Main\Employee\Employee;
use App\Livewire\Page\Main\Employee\HistoryContract;
use App\Livewire\Page\Main\Employee\MyContract;
use App\Livewire\Page\Main\Employee\MyData;
use App\Livewire\Page\Main\Holiday\Holiday;
use App\Livewire\Page\Main\Leave\DetailLeaveType;
use App\Livewire\Page\Main\Leave\LeaveRequest;
use App\Livewire\Page\Main\Leave\LeaveType;
use App\Livewire\Page\Main\Leave\ManagementLeave;
use App\Livewire\Page\Main\Payroll\DetailPayrollEmployee;
use App\Livewire\Page\Main\Payroll\DetailPayrollPeriod;
use App\Livewire\Page\Main\Payroll\EditPayrollEmployee;
use App\Livewire\Page\Main\Payroll\DetailMyPayroll;
use App\Livewire\Page\Main\Payroll\ManagementPayroll;
use App\Livewire\Page\Main\Payroll\MyPayroll;
use App\Livewire\Page\Main\Position\DetailPosition;
use App\Livewire\Page\Main\Position\Position;
use App\Livewire\Page\Main\Roles\CreateRole;
use App\Livewire\Page\Main\Roles\DetailRole;
use App\Livewire\Page\Main\Roles\EditRole;
use App\Livewire\Page\Main\Roles\Roles;
use App\Livewire\Page\Main\Team\DetailTeam;
use App\Livewire\Page\Main\Team\Team;
use App\Livewire\Page\Main\Time\Time;
use App\Livewire\Page\Main\User\DetailRolePermission;
use App\Livewire\Page\Main\User\DetailUser;
use App\Livewire\Page\Main\User\User;
use App\Livewire\Page\Main\WorkManagement\CreateDivisionProject;
use App\Livewire\Page\Main\WorkManagement\CreateMasterProject;
use App\Livewire\Page\Main\WorkManagement\CreateTask;
use App\Livewire\Page\Main\WorkManagement\DivisionProjectDetail;
use App\Livewire\Page\Main\WorkManagement\MasterProjectApprove;
use App\Livewire\Page\Main\WorkManagement\MasterProjectDetail;
use App\Livewire\Page\Main\WorkManagement\MasterProjects;
use App\Livewire\Page\Main\WorkManagement\MyTasks;
use App\Livewire\Page\Main\WorkManagement\TaskDetail;

Route::get('/', function () {
    return redirect()->route("login");
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});


Route::middleware(['auth', 'isActive'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/my-profile', MyProfile::class)->middleware("gateMyProfile")->name('my-profile');
    Route::get('/my-data', MyData::class)->middleware("permission:view-data-my")->name('my-data');
    Route::get('/my-contract', MyContract::class)->middleware("permission:view-contract-my")->name('my-contract');
    Route::get('/my-contract/{contract}', DetailMyContract::class)->middleware("permission:show-contract-my")->name('my-contract.show');

    Route::prefix('print')->group(function () {
        Route::get('employee/{employee}/contract/{contract}', PrintContractEmployeeController::class)->name('print.contract.employee');


        Route::get('/warning-letter/{warningLetter}/print', PrintWarningLetterController::class)
            ->middleware('permission:show-warning-letter')
            ->name('print.warning-letter');

        Route::get('/payroll/{period}/print/slip/{payroll}', PrintPayrollSlipController::class)
            ->name('payroll.print.slip');

        Route::get('/payroll/{period}/print/summary', PrintPayrollSummaryController::class)
            ->name('payroll.print.summary');

        Route::get('/payroll/{period}/print/payments', PrintPayrollPaymentController::class)
            ->name('payroll.print.payments');
    });

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
        // untuk edit
        Route::get('/{employee}/edit', EditEmployee::class)->middleware('permission:update-employee')->name('employee.edit');

        // untuk detail
        Route::get('/{employee}/detail', DetailEmployee::class)->middleware('permission:show-employee')->name('employee.show');

        // untuk buat contract
        Route::get('/{employee}/contract/create', CreateEmployeeContract::class)->middleware('permission:create-contract')->name('contract.create');

        // untuk detail contract
        Route::get('/{employee}/contract/{contract}/detail', DetailEmployeeContract::class)->middleware('permission:show-contract')->name('contract.show');

        // untuk edit contract
        Route::get('/{employee}/contract/edit', EditEmployeeContract::class)->middleware('permission:update-contract')->name('contract.edit');
    });

    Route::prefix('user')->group(function () {
        Route::get('/', User::class)->middleware('permission:view-user')->name('user.view');
        Route::get('/{user}/detail', DetailUser::class)->middleware('permission:show-user')->name('user.show');
        Route::get('/{user}/role/detail', DetailRolePermission::class)->middleware('permission:show-user')->name('user.role.show');
    });

    Route::prefix('benefits')->group(function () {
        Route::get('/', Benefit::class)->middleware('permission:view-benefit')->name('benefit.view');
        Route::get('/{benefit}/detail', DetailBenefit::class)->middleware('permission:show-benefit')->name('benefit.show');
    });

    Route::prefix('positions')->group(function () {
        Route::get('/', Position::class)->middleware('permission:view-position')->name('position.view');
        Route::get('/{position}/detail', DetailPosition::class)->middleware("permission:show-position")->name('position.show');
    });

    Route::prefix('roles')->group(function () {
        Route::get('/', Roles::class)->middleware('permission:view-role')->name('role.view');
        Route::get('/create', CreateRole::class)->middleware('permission:create-role')->name('role.create');
        Route::get('/{role}/detail', DetailRole::class)->middleware('permission:show-role')->name('role.show');
        Route::get('/{role}/edit', EditRole::class)->middleware('permission:update-role')->name('role.edit');
    });

    Route::prefix('time')->group(function () {
        Route::get('/', Time::class)->middleware('permission:view-work-time')->name('time.view');
    });

    Route::prefix('holiday')->group(function () {
        Route::get("/", Holiday::class)->middleware("permission:view-holiday")->name("holiday.view");
    });

    Route::prefix('attendance')->group(function () {
        Route::get('/', Attendances::class)->middleware('permission:view-attendance')->name('attendance.view');
        Route::get('/monitoring', MonitorAttendance::class)->middleware('permission:view-monitor-attendance')->name('attendance.monitor.view');
        Route::get('/daily-status', DailyStatus::class)->middleware('permission:view-status-daily')->name('attendance.daily-status.view');
        Route::get('/manage/history', HistoryAttendanceManage::class)->middleware('permission:history-attendance')->name('attendance.history.view');
        Route::get('/history', HistoryAttendances::class)->middleware('permission:view-attendance')->name('history.view');
    });

    Route::prefix('leave')->group(function () {
        Route::get('/', LeaveRequest::class)->middleware('permission:view-leave')->name('leave.view');
        Route::get('/manage', ManagementLeave::class)->middleware('permission:view-management-leave')->name('leave.manage.view');

        Route::get('/type', LeaveType::class)->middleware('permission:view-type-leave')->name('leave.type.view');
        Route::get('/type/{leavetype}', DetailLeaveType::class)->middleware('permission:show-type-leave')->name('leave.type.show');
        Route::get('/absence', Absence::class)->middleware('permission:view-manage-absence')->name('absence.view');
    });

    Route::prefix('contract')->group(function () {
        Route::get('/', Contract::class)->middleware('permission:view-contract')->name('contract.view');
    });

    Route::prefix('payroll')->group(function () {
        Route::get('/my', MyPayroll::class)
            ->name('payroll.my.view')
            ->middleware('permission:view-payroll-my');

        Route::get('/my/{payroll}', DetailMyPayroll::class)
            ->name('payroll.my.show')
            ->middleware('permission:show-payroll-my');

        Route::get('/', ManagementPayroll::class)
            ->name('payroll.view')
            ->middleware('permission:view-payroll');

        Route::get('/{period}', DetailPayrollPeriod::class)
            ->name('payroll.show')
            ->middleware('permission:show-payroll');

        Route::get(
            '/{period}/employee/{payroll}',
            DetailPayrollEmployee::class
        )
            ->middleware('permission:show-payroll')
            ->name('payroll.employee.show');


        Route::get(
            '/payroll/{period}/employee/{payroll}/edit',
            EditPayrollEmployee::class
        )
            ->middleware('permission:edit-period-payroll')
            ->name('payroll.employee.edit');
    });


    Route::prefix('work-management')->group(function () {
        Route::get('/master-projects', MasterProjects::class)->middleware('permission:view-master-project')->name('work-management.master-projects');
        Route::get('/master-projects/create', CreateMasterProject::class)->middleware('permission:create-master-project')->name('work-management.master-projects.create');
        Route::get('/master-projects/{masterProject}', MasterProjectDetail::class)->middleware('permission:view-master-project')->name('work-management.master-projects.show');
        Route::get('/master-projects/{masterProject}/division-projects/create', CreateDivisionProject::class)->middleware('permission:create-division-project')->name('work-management.master-projects.division-projects.create');
        Route::get('/master-projects/{masterProject}/approve', MasterProjectApprove::class)->middleware('permission:approve-master-project')->name('work-management.master-projects.approve');
        Route::get('/division-projects/{divisionProject}', DivisionProjectDetail::class)->middleware('permission:view-division-project')->name('work-management.division-projects.show');
        Route::get('/division-projects/{divisionProject}/tasks/create', CreateTask::class)->middleware('permission:create-task')->name('work-management.division-projects.tasks.create');
        Route::get('/tasks', MyTasks::class)->middleware('permission:view-task')->name('work-management.tasks');
        Route::get('/tasks/{task}', TaskDetail::class)->middleware('permission:view-task')->name('work-management.tasks.show');
    });
    Route::prefix('discipline')->group(function () {
        Route::get('/late', LateDiciplineRule::class)
            ->middleware('permission:view-late-discipline-rule')
            ->name('discipline.late.view');
        Route::get('/unpresent', UnpresentDisciplineRule::class)
            ->middleware('permission:view-unpresent-discipline-rule')
            ->name('discipline.unpresent.view');
        Route::get('/warning-letter', WarningLetter::class)
            ->middleware('permission:view-warning-letter')
            ->name('discipline.warning-letter.view');
        Route::get('/warning-letter/{warningLetter}', DetailWarningLetter::class)
            ->middleware('permission:show-warning-letter')
            ->name('discipline.warning-letter.show');
    });
});
