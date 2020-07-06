<?php

namespace App\MyPhone\Sms\Http\Controllers;

use App\Http\Controllers\Controller;
use Sms;

class SmsController extends Controller
{

    public function receive($provider)
    {
        return Sms::with($provider)->receive();
    }


    public function delivery($provider)
    {
        return Sms::with($provider)->track();
    }
}