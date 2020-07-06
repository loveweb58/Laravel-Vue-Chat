<?php

namespace App\MyPhone\Sms\Contracts;

interface Factory
{

    /**
     * Get an Sms provider implementation.
     *
     * @param  string $driver
     *
     * @return \App\MyPhone\Sms\Contracts\Provider
     */
    public function driver($driver = null);
}