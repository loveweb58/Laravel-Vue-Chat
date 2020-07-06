<?php

namespace App\MyPhone\Sms\Contracts;

interface Provider
{

    /**
     * Send Sms
     *
     * @param array $receivers
     * @param       $text
     * @param       $sender
     * @param array $parameters
     *
     * @return mixed
     */
    public function send(array $receivers, $text, $sender, array $parameters = []);


    /**
     * Track Sms
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function track(array $parameters = []);


    /**
     * Receive Messages
     *
     * @param array $parameters
     *
     * @return array
     */
    public function receive(array $parameters = []);

}