<?php

namespace App\Livewire\Page\Main\Dashboard;

use App\Models\Employees;
use App\Models\EmployeeContract;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $admin = $user->hasRole('administrator');
        $metrics = [];
        $expiring = collect();
        if ($admin) {
            $metrics = ['Total akun HRD' => User::role('hr')->count(), 'Aktif' => User::role('hr')->where('status', 'active')->count(), 'Nonaktif' => User::role('hr')->where('status', 'inactive')->count(), 'Pending' => User::role('hr')->where('status', 'pending')->count()];

            return view('livewire.page.main.dashboard.dashboard', compact('admin', 'metrics', 'expiring'));
        }

        abort_unless($user->hasRole('hr'), 403);

        $today = today();
        $greeting = match (true) {
            now()->hour < 12 => 'Selamat pagi',
            now()->hour < 15 => 'Selamat siang',
            now()->hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        $employeeSummary = [];
        $contractSummary = [];
        $expiring = collect();
        $expiringCount = 0;

        if ($user->can('view-employee')) {
            $counts = Employees::query()->selectRaw('status_employee, count(*) as total')
                ->groupBy('status_employee')->pluck('total', 'status_employee');
            $employeeSummary = [
                'total' => (int) $counts->sum(),
                'active' => (int) ($counts['active'] ?? 0),
                'onboarding' => (int) ($counts['onboarding'] ?? 0),
                'inactive' => (int) ($counts['inactive'] ?? 0),
                'resign' => (int) ($counts['resign'] ?? 0),
                'terminated' => (int) ($counts['terminated'] ?? 0),
            ];
        }

        if ($user->can('view-contract')) {
            $counts = EmployeeContract::query()->selectRaw('status, count(*) as total')
                ->groupBy('status')->pluck('total', 'status');
            $contractSummary = [
                'total' => (int) $counts->sum(),
                'active' => (int) ($counts['active'] ?? 0),
                'draft' => (int) ($counts['draft'] ?? 0),
                'expired' => (int) ($counts['expired'] ?? 0),
                'terminated' => (int) ($counts['terminated'] ?? 0),
            ];
            $query = EmployeeContract::query()->where('status', 'active')
                ->whereBetween('end_date', [$today, $today->copy()->addDays(30)]);
            $expiringCount = (clone $query)->count();
            if ($user->can('show-contract')) {
                $expiring = $query->with('employees')->orderBy('end_date')->limit(10)->get();
            }
        }

        return view('livewire.page.main.dashboard.human-resource', compact(
            'today', 'greeting', 'employeeSummary', 'contractSummary', 'expiring', 'expiringCount'
        ));
    }
}
