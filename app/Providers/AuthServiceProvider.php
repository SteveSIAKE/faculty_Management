<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Attendance;
use App\Policies\AttendancePolicy;

class AuthServiceProvider extends ServiceProvider
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
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

    protected function registerPolicies()
    {
        $this->app->bind(AttendancePolicy::class, function ($app) {
            return new AttendancePolicy();
        });
    }
} 