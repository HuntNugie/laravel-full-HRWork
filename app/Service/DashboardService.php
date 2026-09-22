<?php

namespace App\Service;

use App\Models\User;

class DashboardService
{
    public static function matching(User $user): string
    {
        return match (true) {
            $user->hasRole('super-admin') =>
                'livewire.page.main.dashboard.super-admin',

            $user->hasAnyRole(['Employee', 'employee']) =>
                'livewire.page.main.dashboard.employee',

            default =>
                'livewire.page.main.dashboard.dashboard',
        };
    }
}
