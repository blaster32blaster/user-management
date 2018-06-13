<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();

        Passport::tokensExpireIn(Carbon::now()->addMinutes(10));

        Passport::refreshTokensExpireIn(Carbon::now()->addHours(3));

        Passport::tokensCan([
            'manage-admins' => 'Manage Platform Administrators',
            'manage-client-admins' => 'Manage Client Administrators',
            'manage-users' => 'Manage Users',
            'manage-client-users' => 'Manage Client Users',
            'manage-access' => 'Manage Content Access',
            'manage-client-access' => 'Manage Client Content Access',
            'view-public-content' => 'View Public Platform Content',
            'view-private-content' => 'View Private Platform Content',
            'view-public-client-content' => 'View Public Client Content',
            'view-private-client-content' => 'View Private Client Content',
        ]);
    }
}
