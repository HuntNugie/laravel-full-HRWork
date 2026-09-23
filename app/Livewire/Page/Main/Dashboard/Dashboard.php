<?php

namespace App\Livewire\Page\Main\Dashboard;

use App\Models\Attendances;
use App\Models\Divisi;
use App\Models\DivisionProject;
use App\Models\EmployeeAbsenceRequest;
use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
use App\Models\LeaveRequest;
use App\Models\MasterProject;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\ProjectReport;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Service\DashboardService;
use App\Service\EmployeeDailyStatusService;
use App\Service\LeaveRequestService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $view = DashboardService::matching($user);

        if ($user->hasRole('super-admin')) {
            return $this->renderSuperAdminDashboard($view);
        }

        if (! $user->hasAnyRole(['Employee', 'employee'])) {
            return view($view);
        }

        $employee = $user->employees()
            ->with([
                'user',
                'position',
                'team.divisi',
            ])
            ->firstOrFail();

        $managerDivision = null;
        $managerAttendanceRows = collect();
        $managerAttendanceSummary = [
            'teams' => 0,
            'employees' => 0,
            'checked_in' => 0,
            'late' => 0,
        ];

        $supervisorTeam = null;
        $supervisorAttendanceRows = collect();
        $supervisorAttendanceSummary = [
            'employees' => 0,
            'checked_in' => 0,
            'late' => 0,
        ];

        if ($user->hasRole('manager')) {
            $managerDivision = $employee->managedDivisi()
                ->with([
                    'team.employees.user',
                    'team.employees.position',
                    'team.employees.team',
                ])
                ->first();

            $managerTeams = $managerDivision?->team ?? collect();
            $managerEmployees = $managerTeams
                ->flatMap(fn ($team) => $team->employees)
                ->unique('id')
                ->values();

            $managerAttendanceRows = $this->makeAttendanceRows($managerEmployees);

            $managerAttendanceSummary = [
                'teams' => $managerTeams->count(),
                'employees' => $managerEmployees->count(),
                'checked_in' => $managerAttendanceRows
                    ->whereNotNull('attendance')
                    ->count(),
                'late' => $managerAttendanceRows
                    ->where('status', 'late')
                    ->count(),
            ];
        }

        if ($user->hasRole('supervisor')) {
            $supervisorTeam = $employee->supervisorTeam()
                ->with([
                    'divisi',
                    'employees.user',
                    'employees.position',
                ])
                ->first();

            $supervisorEmployees = $supervisorTeam?->employees
                ->reject(fn ($member) => (int) $member->id === (int) $employee->id)
                ->values() ?? collect();

            $supervisorAttendanceRows = $this->makeAttendanceRows($supervisorEmployees);

            $supervisorAttendanceSummary = [
                'employees' => $supervisorEmployees->count(),
                'checked_in' => $supervisorAttendanceRows
                    ->whereNotNull('attendance')
                    ->count(),
                'late' => $supervisorAttendanceRows
                    ->where('status', 'late')
                    ->count(),
            ];
        }

        $dailyStatusService = app(EmployeeDailyStatusService::class);
        $leaveRequestService = app(LeaveRequestService::class);

        $today = now()->startOfDay();

        $todayStatus = $dailyStatusService->getStatus(
            employee: $employee,
            date: $today,
        );

        $todayAttendance = $employee->attendances()
            ->whereDate('date', $today->toDateString())
            ->first();

        $todayWorkTime = $todayStatus['work_time_id']
            ? \App\Models\WorkTime::query()->find($todayStatus['work_time_id'])
            : null;

        $monthStatuses = $dailyStatusService->getStatuses(
            employee: $employee,
            startDate: now()->startOfMonth(),
            endDate: now()->endOfMonth(),
        );

        $attendanceSummary = [
            'present' => $monthStatuses
                ->whereIn('status', [
                    EmployeeDailyStatusService::STATUS_PRESENT,
                    EmployeeDailyStatusService::STATUS_LATE,
                ])
                ->count(),

            'late' => $monthStatuses
                ->where('status', EmployeeDailyStatusService::STATUS_LATE)
                ->count(),

            'leave' => $monthStatuses
                ->where('status', EmployeeDailyStatusService::STATUS_PAID_LEAVE)
                ->count(),

            'absence' => $monthStatuses
                ->whereIn('status', [
                    EmployeeDailyStatusService::STATUS_ABSENCE_SICK,
                    EmployeeDailyStatusService::STATUS_ABSENCE_PERMIT,
                ])
                ->count(),

            'unpresent' => $monthStatuses
                ->where('status', EmployeeDailyStatusService::STATUS_UNPRESENT)
                ->count(),
        ];

        $currentContract = $leaveRequestService->currentActiveContract($employee);

        $leaveBreakdown = collect();
        $leaveSummary = [
            'quota' => 0,
            'used' => 0,
            'pending' => 0,
            'remaining' => 0,
        ];

        if ($currentContract) {
            $leaveBreakdown = $currentContract
                ->contractLeave()
                ->with('leaveType')
                ->get()
                ->map(function ($entitlement) use (
                    $leaveRequestService,
                    &$leaveSummary
                ) {
                    $used = $leaveRequestService->usedDays(
                        entitlement: $entitlement,
                        year: now()->year,
                    );

                    $pending = $leaveRequestService->pendingDays(
                        entitlement: $entitlement,
                        year: now()->year,
                    );

                    $remaining = max(
                        0,
                        (int) $entitlement->days - $used - $pending
                    );

                    $leaveSummary['quota'] += (int) $entitlement->days;
                    $leaveSummary['used'] += $used;
                    $leaveSummary['pending'] += $pending;
                    $leaveSummary['remaining'] += $remaining;

                    return [
                        'name' => $entitlement->leaveType?->name ?? 'Jenis Cuti',
                        'quota' => (int) $entitlement->days,
                        'used' => $used,
                        'pending' => $pending,
                        'remaining' => $remaining,
                    ];
                });
        }

        $latestLeaveRequest = $employee->leaveRequest()
            ->with('leaveType')
            ->latest('created_at')
            ->first();

        $pendingLeaveCount = $employee->leaveRequest()
            ->where('status', 'pending')
            ->count();

        $pendingAbsenceCount = $employee->employeeAbsenceRequest()
            ->where('status', 'pending')
            ->count();

        $issuedWarningLettersCount = EmployeeWarningLetter::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'issued')
            ->count();

        $latestWarningLetter = EmployeeWarningLetter::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'issued')
            ->latest('issued_date')
            ->latest('id')
            ->first();

        $latestPayroll = Payroll::query()
            ->with('period')
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['processed', 'paid'])
            ->whereHas(
                'period',
                fn ($query) => $query->whereIn('status', ['processed', 'paid'])
            )
            ->latest('paid_at')
            ->latest('created_at')
            ->first();

        $greeting = match (true) {
            now()->hour < 12 => 'Selamat pagi',
            now()->hour < 15 => 'Selamat siang',
            now()->hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        $statusMeta = match ($todayStatus['status']) {
            EmployeeDailyStatusService::STATUS_PRESENT => [
                'label' => 'Hadir',
                'intent' => 'success',
            ],
            EmployeeDailyStatusService::STATUS_LATE => [
                'label' => 'Terlambat',
                'intent' => 'warning',
            ],
            EmployeeDailyStatusService::STATUS_PAID_LEAVE => [
                'label' => 'Cuti',
                'intent' => 'info',
            ],
            EmployeeDailyStatusService::STATUS_ABSENCE_SICK => [
                'label' => 'Sakit',
                'intent' => 'warning',
            ],
            EmployeeDailyStatusService::STATUS_ABSENCE_PERMIT => [
                'label' => 'Izin',
                'intent' => 'warning',
            ],
            EmployeeDailyStatusService::STATUS_HOLIDAY => [
                'label' => 'Hari Libur',
                'intent' => 'neutral',
            ],
            EmployeeDailyStatusService::STATUS_NON_WORKING => [
                'label' => 'Tidak Ada Jadwal',
                'intent' => 'neutral',
            ],
            EmployeeDailyStatusService::STATUS_OUTSIDE_CONTRACT => [
                'label' => 'Di Luar Kontrak',
                'intent' => 'danger',
            ],
            EmployeeDailyStatusService::STATUS_UNPRESENT => [
                'label' => 'Tidak Hadir',
                'intent' => 'danger',
            ],
            default => [
                'label' => 'Menunggu',
                'intent' => 'info',
            ],
        };

        return view($view, [
            'employee' => $employee,
            'greeting' => $greeting,
            'today' => $today,
            'todayStatus' => $todayStatus,
            'todayStatusLabel' => $statusMeta['label'],
            'todayStatusIntent' => $statusMeta['intent'],
            'todayAttendance' => $todayAttendance,
            'todayWorkTime' => $todayWorkTime,
            'attendanceSummary' => $attendanceSummary,
            'currentContract' => $currentContract,
            'leaveBreakdown' => $leaveBreakdown,
            'leaveSummary' => $leaveSummary,
            'latestLeaveRequest' => $latestLeaveRequest,
            'pendingLeaveCount' => $pendingLeaveCount,
            'pendingAbsenceCount' => $pendingAbsenceCount,
            'latestPayroll' => $latestPayroll,
            'issuedWarningLettersCount' => $issuedWarningLettersCount,
            'latestWarningLetter' => $latestWarningLetter,
            'managerDivision' => $managerDivision,
            'managerAttendanceRows' => $managerAttendanceRows,
            'managerAttendanceSummary' => $managerAttendanceSummary,
            'supervisorTeam' => $supervisorTeam,
            'supervisorAttendanceRows' => $supervisorAttendanceRows,
            'supervisorAttendanceSummary' => $supervisorAttendanceSummary,
        ]);
    }

    private function renderSuperAdminDashboard(string $view)
    {
        $today = now()->startOfDay();

        $totalUsers = User::query()->count();
        $activeUsers = User::query()->where('status', 'active')->count();
        $pendingUsers = User::query()->where('status', 'pending')->count();
        $inactiveUsers = User::query()->where('status', 'non-active')->count();

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
            'active' => DivisionProject::query()->where('status', '!=', 'completed')->count(),
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
            'divisions' => Divisi::query()->count(),
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

        return view($view, [
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

    private function makeAttendanceRows(Collection $employees): Collection
    {
        if ($employees->isEmpty()) {
            return collect();
        }

        $attendanceByEmployee = Attendances::query()
            ->whereIn('employee_id', $employees->pluck('id'))
            ->whereDate('date', now()->toDateString())
            ->get()
            ->keyBy('employee_id');

        return $employees->map(function ($member) use ($attendanceByEmployee) {
            $attendance = $attendanceByEmployee->get($member->id);

            return [
                'employee' => $member,
                'attendance' => $attendance,
                'status' => $attendance?->status,
                'status_label' => match ($attendance?->status) {
                    'present' => 'Hadir',
                    'late' => 'Terlambat',
                    default => 'Belum Check In',
                },
            ];
        })->values();
    }
}
