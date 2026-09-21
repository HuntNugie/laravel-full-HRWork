<?php

namespace App\Providers;

use App\Models\DivisionProject;
use App\Models\MasterProject;
use App\Models\Task;
use App\Policies\DivisionProjectPolicy;
use App\Policies\MasterProjectPolicy;
use App\Policies\RolePolicy;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(MasterProject::class, MasterProjectPolicy::class);
        Gate::policy(DivisionProject::class, DivisionProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
    }
}
