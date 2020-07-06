<?php

namespace App\MyPhone\Sms;

use App\MyPhone\Sms\Contracts\Factory;
use App\MyPhone\Sms\Providers\AeriaLink;
use Illuminate\Support\Manager;

class SmsManager extends Manager implements Factory
{

    /**
     * Get a driver instance.
     *
     * @param  string $driver
     *
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }


    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'viber';
    }


    /**
     * Create an instance of the specified driver.
     *
     * @return AeriaLink
     */
    protected function createAeriaLinkDriver()
    {
        $config = $this->app['config']['services.sms.aerialink'] ?? [];

        return $this->buildProvider('App\MyPhone\Sms\Providers\AeriaLink', $config);
    }


    /**
     * Build an Sms provider instance.
     *
     * @param  string $provider
     * @param  array  $config
     *
     * @return mixed
     */
    public function buildProvider($provider, $config)
    {
        return new $provider($config);
    }

}