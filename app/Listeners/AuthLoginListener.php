<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;

class AuthLoginListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Handle the event.
     *
     * @param  Login $event
     *
     * @return void
     */
    public function handle(Login $event)
    {
        if ($event->user instanceof User) {
            $event->user->log('Auth from :ip', ['ip' => request()->ip()], 'auth');
        }
    }
}
