<?php

namespace App\Providers;

use App\Livewire\Page\Main\WorkManagement\CreateMasterProject;
use App\Livewire\Page\Main\WorkManagement\MasterProjects;
use App\Models\DivisionProject;
use App\Models\MasterProject;
use App\Models\Task;
use App\Policies\DivisionProjectPolicy;
use App\Policies\MasterProjectPolicy;
use App\Policies\RolePolicy;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(MasterProject::class, MasterProjectPolicy::class);
        Gate::policy(DivisionProject::class, DivisionProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);

        Route::middleware(['web', 'auth', 'isActive'])->group(function (): void {
            Route::get('/work-management/master-projects', MasterProjects::class)
                ->middleware('permission:view-master-project')
                ->name('work-management.master-projects');

            Route::get('/work-management/master-projects/create', CreateMasterProject::class)
                ->middleware('permission:create-master-project')
                ->name('work-management.master-projects.create');
        });
    }
}
