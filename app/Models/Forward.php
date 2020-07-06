<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Forward
 *
 * @property int $id
 * @property int $account_id
 * @property int $did_id
 * @property int $tmp_did_id
 * @property string|null $forward_to
 * @property string|null $forward_from
 * @property int $enabled
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @property-read \App\Models\Did $did
 * @property-read \App\Models\Did $number
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Forward whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Forward whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Forward whereDidId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Forward whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Forward whereForwardFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Forward whereForwardTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Forward whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Forward whereTmpDidId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Forward whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Forward extends Model
{

    protected $table = 'sms_forwarding';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function account()
    {
        return $this->belongsTo(Account::class);
    }


    public function did()
    {
        return $this->belongsTo(Did::class, 'did_id');
    }


    public function number()
    {
        return $this->belongsTo(Did::class, 'tmp_did_id');
    }
}
