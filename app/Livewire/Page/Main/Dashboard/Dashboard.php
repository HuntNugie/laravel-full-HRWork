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
        } else {
            if ($user->can('view-employee')) {
                $metrics += ['Total karyawan' => Employees::count(), 'Karyawan aktif' => Employees::where('status_employee', 'active')->count()];
            }
            if ($user->can('view-contract')) {
                $metrics += ['Kontrak aktif' => EmployeeContract::where('status', 'active')->count(), 'Draft kontrak' => EmployeeContract::where('status', 'draft')->count(), 'Kontrak berakhir' => EmployeeContract::where('status', 'expired')->count()];
                $expiring = EmployeeContract::with('employees')->where('status', 'active')->whereBetween('end_date', [today(), today()->addDays(30)])->orderBy('end_date')->limit(10)->get();
            }
        }
        return view('livewire.page.main.dashboard.dashboard', compact('admin', 'metrics', 'expiring'));
    }
}
