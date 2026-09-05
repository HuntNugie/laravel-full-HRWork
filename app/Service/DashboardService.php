<?php

namespace App\Service;

use App\Models\User;

class DashboardService
{
    static function matching(User $user)
    {
        return match (true) {
            $user->hasRole('employee') => 'livewire.page.main.dashboard.employee',
            $user->hasRole('super-admin') => 'livewire.page.main.dashboard.super-admin',
            default => 'livewire.page.main.dashboard.dashboard'
        };
    }
}
