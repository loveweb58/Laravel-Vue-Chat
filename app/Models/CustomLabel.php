<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomLabel
 *
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property string $type
 * @property mixed|null $payload
 * @property string|null $default
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomLabel whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomLabel whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomLabel wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomLabel whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomLabel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomLabel extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $table = "custom_labels";


    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
