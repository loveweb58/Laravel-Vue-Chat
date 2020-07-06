<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Contact'         => 'App\Policies\ContactPolicy',
        'App\Models\Group'           => 'App\Policies\GroupPolicy',
        'App\Models\Role'            => 'App\Policies\RolesPolicy',
        'App\Models\User'            => 'App\Policies\UserPolicy',
        'App\Models\Appointment'     => 'App\Policies\AppointmentPolicy',
        'App\Models\Forward'         => 'App\Policies\ForwardPolicy',
        'App\Models\AutoReply'       => 'App\Policies\AutoReplyPolicy',
        'App\Models\CustomLabel'     => 'App\Policies\CustomLabelPolicy',
        'App\Models\MessageTemplate' => 'App\Policies\MessageTemplatePolicy',
        'App\Models\Account'         => 'App\Policies\AccountPolicy',
        'App\Models\Blacklist'       => 'App\Policies\BlacklistPolicy',
    ];


    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
