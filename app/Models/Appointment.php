<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Appointment
 *
 * @property int $id
 * @property int $account_id
 * @property \Carbon\Carbon $date
 * @property string|null $number
 * @property string|null $name
 * @property string|null $subject
 * @property string|null $description
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Appointment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Appointment extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $dates = ['date'];


    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
