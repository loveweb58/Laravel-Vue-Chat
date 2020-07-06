<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Did
 *
 * @property int $id
 * @property int $account_id
 * @property string $did
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Did whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Did whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Did whereDid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Did whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Did whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Did extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $table = 'did';


    public function users()
    {
        return $this->belongsToMany(User::class, 'user_did');
    }


    public function account()
    {
        return $this->belongsTo(Account::class);
    }

}
