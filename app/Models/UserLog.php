<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserLog
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $type
 * @property string $text
 * @property array $payload
 * @property string|null $ip
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read mixed $description
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLog whereUserId($value)
 * @mixin \Eloquent
 */
class UserLog extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user_logs";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = ['payload' => 'array'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function getDescriptionAttribute()
    {
        return __($this->text, $this->payload);
    }

}
