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
use Illuminate\Support\Facades\URL;
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
        // Cloudflare terminates HTTPS before the request reaches Laravel.
        // Force generated URLs to HTTPS in production so Livewire's update
        // endpoint is not rendered as mixed active content when proxy headers
        // are unavailable or rewritten by the origin web server.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(MasterProject::class, MasterProjectPolicy::class);
        Gate::policy(DivisionProject::class, DivisionProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
    }
}
