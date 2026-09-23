<?php

namespace App\Livewire\Page\Main\Dashboard;

use App\Models\Attendances;
use App\Models\DivisionProject;
use App\Models\EmployeeAbsenceRequest;
use App\Models\Employees;
use App\Models\LeaveRequest;
use App\Models\MasterProject;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\ProjectReport;
use App\Models\Task;
use App\Models\Team;
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
        $inactiveUsers = User::query()->where('status', 'non-active')->count();

        $activeEmployees = Employees::query()
            ->whereHas('user', fn ($query) => $query->where('status', 'active'))
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

        $pendingProjectReports = ProjectReport::query()
            ->where('status', ProjectReport::STATUS_SUBMITTED)
            ->count();

        $masterReadyForReview = MasterProject::query()
            ->where('status', 'ready_for_review')
            ->count();

        $pendingActions = $pendingUsers
            + $pendingLeave
            + $pendingAbsence
            + $pendingProjectReports
            + $masterReadyForReview;

        $masterProjects = [
            'total' => MasterProject::query()->count(),
            'active' => MasterProject::query()->where('status', '!=', 'completed')->count(),
            'ready_for_review' => $masterReadyForReview,
            'completed' => MasterProject::query()->where('status', 'completed')->count(),
        ];

        $divisionProjects = [
            'total' => DivisionProject::query()->count(),
            'active' => DivisionProject::query()->whereNotIn('status', ['completed'])->count(),
            'submitted_to_gm' => DivisionProject::query()->where('status', 'submitted_to_gm')->count(),
            'revision_required' => DivisionProject::query()->where('status', 'revision_required')->count(),
            'completed' => DivisionProject::query()->where('status', 'completed')->count(),
        ];

        $tasks = [
            'total' => Task::query()->count(),
            'open' => Task::query()
                ->whereNotIn('status', [Task::STATUS_DONE, Task::STATUS_CANCELLED])
                ->count(),
            'in_review' => Task::query()->where('status', Task::STATUS_IN_REVIEW)->count(),
            'done' => Task::query()->where('status', Task::STATUS_DONE)->count(),
            'blocked' => Task::query()->where('status', Task::STATUS_BLOCKED)->count(),
        ];

        $reportQueue = [
            'supervisor' => ProjectReport::query()
                ->where('report_level', ProjectReport::LEVEL_SUPERVISOR)
                ->where('status', ProjectReport::STATUS_SUBMITTED)
                ->count(),
            'manager' => ProjectReport::query()
                ->where('report_level', ProjectReport::LEVEL_MANAGER)
                ->where('status', ProjectReport::STATUS_SUBMITTED)
                ->count(),
            'approved' => ProjectReport::query()
                ->where('status', ProjectReport::STATUS_APPROVED)
                ->count(),
            'rejected' => ProjectReport::query()
                ->where('status', ProjectReport::STATUS_REJECTED)
                ->count(),
        ];

        $organization = [
            'divisions' => \App\Models\Divisi::query()->count(),
            'teams' => Team::query()->count(),
            'positions' => Position::query()->count(),
            'roles' => Role::query()->count(),
        ];

        $latestEmployees = Employees::query()
            ->with(['user', 'position'])
            ->latest()
            ->limit(6)
            ->get();

        $recentProjectReports = ProjectReport::query()
            ->with(['reporter', 'divisionProject'])
            ->latest()
            ->limit(5)
            ->get();

        $latestPayrollPeriod = PayrollPeriod::query()
            ->latest('end_date')
            ->first();

        $latestPayrollSummary = [
            'period' => $latestPayrollPeriod,
            'total' => $latestPayrollPeriod?->payrolls()->count() ?? 0,
            'processed' => $latestPayrollPeriod?->payrolls()->whereIn('status', ['processed', 'paid'])->count() ?? 0,
            'paid' => $latestPayrollPeriod?->payrolls()->where('status', 'paid')->count() ?? 0,
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
            'pendingProjectReports' => $pendingProjectReports,
            'masterReadyForReview' => $masterReadyForReview,
            'pendingActions' => $pendingActions,
            'masterProjects' => $masterProjects,
            'divisionProjects' => $divisionProjects,
            'tasks' => $tasks,
            'reportQueue' => $reportQueue,
            'organization' => $organization,
            'latestEmployees' => $latestEmployees,
            'recentProjectReports' => $recentProjectReports,
            'latestPayrollSummary' => $latestPayrollSummary,
        ]);
    }
}
