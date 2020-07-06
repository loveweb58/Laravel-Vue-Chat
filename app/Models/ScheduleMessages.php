<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\MyPhone\Sms\Helpers\SmsLengthCalculator;
use Illuminate\Database\Eloquent\Builder;
class ScheduleMessages extends Model
{
    protected $table = "schedule_messages";
    protected $primaryKey = "id";
    const UPDATED_AT =null;
    const CREATED_AT =null;
}
