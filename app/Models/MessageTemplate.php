<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MessageTemplate
 *
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property string $text
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MessageTemplate extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $table = "message_templates";


    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
