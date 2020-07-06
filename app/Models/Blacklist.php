<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Blacklist
 *
 * @property int $id
 * @property int $account_id
 * @property string $number
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Account $account
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Blacklist onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Blacklist whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Blacklist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Blacklist whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Blacklist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Blacklist whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Blacklist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Blacklist withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Blacklist withoutTrashed()
 * @mixin \Eloquent
 */
class Blacklist extends Model
{

    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "blacklist";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

}
