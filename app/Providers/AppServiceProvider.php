<?php

namespace App\Providers;

use App\Http\Middleware\isActive;
use App\Support\HrAccess;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            if (in_array($ability, HrAccess::ADMIN, true) && ! $user->hasRole('administrator')) {
                return false;
            }
            if (in_array($ability, HrAccess::hr(), true) && (! $user->hasRole('hr') || $user->hasRole('administrator'))) {
                return false;
            }
            return null;
        });
        Livewire::addPersistentMiddleware([isActive::class, RoleMiddleware::class, PermissionMiddleware::class]);
    }
}
