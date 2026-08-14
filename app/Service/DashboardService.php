<?php namespace App\Service;

use App\Models\User;

class DashboardService{
    static function matching(User $user){
        return match(true){
            $user->hasRole('Admin') => 'livewire.page.main.dashboard.admin',
            $user->hasRole('Manager') => 'livewire.page.main.dashboard.manager',
            $user->hasRole('Employee') => 'livewire.page.main.dashboard.employee',
            default => 'livewire.page.main.dashboard.dashboard'
        };
    }
}