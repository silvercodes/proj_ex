<?php

namespace App\Providers;

use App\Models\Permission;
use App\User;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return false
     */
    public function boot()
    {
        try {
            Permission::get()->map(function($permission) {
                Gate::define($permission->slug, function(User $user) use ($permission) {
                    return $user->hasPermissionComplete($permission);
                });
            });
        } catch (Exception $ex)
        {
            report($ex);
            return false;
        }
    }
}
