<?php

namespace App\MyPhone\Sms\Facades;

use App\MyPhone\Sms\SmsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see SmsManager
 */
class Sms extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'App\MyPhone\Sms\Contracts\Factory';
    }
}