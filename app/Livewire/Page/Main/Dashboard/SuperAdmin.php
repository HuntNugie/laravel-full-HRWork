<?php

namespace App\Livewire\Page\Main\Dashboard;

use App\Models\Attendances;
use App\Models\EmployeeAbsenceRequest;
use App\Models\Employees;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class SuperAdmin extends Component
{
    public function render()
    {
        $today = now()->startOfDay();

        $totalUsers = User::query()->count();
        $activeUsers = User::query()->where('status', 'active')->count();
        $pendingUsers = User::query()->where('status', 'pending')->count();
        $inactiveUsers = User::query()->where('status', 'inactive')->count();

        $activeEmployees = Employees::query()
            ->where('status_employee', 'active')
            ->count();

        $todayAttendance = Attendances::query()
            ->whereDate('date', $today->toDateString());

        $checkedInToday = (clone $todayAttendance)
            ->whereIn('status', ['present', 'late'])
            ->distinct()
            ->count('employee_id');

        $presentToday = (clone $todayAttendance)
            ->where('status', 'present')
            ->distinct()
            ->count('employee_id');

        $lateToday = (clone $todayAttendance)
            ->where('status', 'late')
            ->distinct()
            ->count('employee_id');

        $unrecordedToday = max(0, $activeEmployees - $checkedInToday);

        $pendingLeave = LeaveRequest::query()
            ->where('status', 'pending')
            ->count();

        $pendingAbsence = EmployeeAbsenceRequest::query()
            ->where('status', 'pending')
            ->count();

        $pendingActions = $pendingUsers + $pendingLeave + $pendingAbsence;


        $masterData = [
            'positions' => Position::query()->count(),
            'roles' => Role::query()->count(),
        ];

        $latestEmployees = Employees::query()
            ->with(['user', 'position'])
            ->latest()
            ->limit(6)
            ->get();


        $latestPayrollPeriod = PayrollPeriod::query()
            ->latest('end_date')
            ->first();

        $latestPayrollSummary = [
            'period' => $latestPayrollPeriod,
            'total' => $latestPayrollPeriod?->payrolls()->count() ?? 0,
            'processed' => $latestPayrollPeriod?->payrolls()
                ->whereIn('status', ['processed', 'paid'])
                ->count() ?? 0,
            'paid' => $latestPayrollPeriod?->payrolls()
                ->where('status', 'paid')
                ->count() ?? 0,
        ];

        $greeting = match (true) {
            now()->hour < 12 => 'Selamat pagi',
            now()->hour < 15 => 'Selamat siang',
            now()->hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        return view('livewire.page.main.dashboard.super-admin', [
            'today' => $today,
            'greeting' => $greeting,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'pendingUsers' => $pendingUsers,
            'inactiveUsers' => $inactiveUsers,
            'activeEmployees' => $activeEmployees,
            'checkedInToday' => $checkedInToday,
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'unrecordedToday' => $unrecordedToday,
            'pendingLeave' => $pendingLeave,
            'pendingAbsence' => $pendingAbsence,
            'pendingActions' => $pendingActions,
            'masterData' => $masterData,
            'latestEmployees' => $latestEmployees,
            'latestPayrollSummary' => $latestPayrollSummary,
        ]);
    }
}
