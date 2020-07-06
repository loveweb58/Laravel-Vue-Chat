<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Package
 *
 * @property int $id
 * @property string $name
 * @property array $limits
 * @property float $monthly_fee
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Account[] $accounts
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Package whereLimits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Package whereMonthlyFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Package whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Package extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $casts = ['limits' => 'json'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function accounts()
    {
        return $this->hasMany(Account::class);
    }


    public function limits($type, $default = 0)
    {
        return $this->limits[$type] ?? $default;
    }
}
