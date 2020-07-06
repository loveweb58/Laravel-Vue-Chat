<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AutoReply
 *
 * @property int $id
 * @property string|null $keyword
 * @property int $account_id
 * @property int $did_id
 * @property int|null $parent_id
 * @property string $source
 * @property string|null $text
 * @property string|null $mms
 * @property string|null $info
 * @property string|null $action
 * @property array $date
 * @property array $weekdays
 * @property int $order
 * @property int $enabled
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AutoReply[] $children
 * @property-read \App\Models\Did $did
 * @property-read \App\Models\AutoReply|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereDidId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereMms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoReply whereWeekdays($value)
 * @mixin \Eloquent
 */
class AutoReply extends Model
{

    protected $table = 'auto_reply';

    protected $with = ['children'];

    protected $casts = ['date' => 'json', 'weekdays' => 'json'];

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


    public function children()
    {
        return $this->hasMany(AutoReply::class, 'parent_id')->with('children');
    }


    public function parent()
    {
        return $this->belongsTo(AutoReply::class, 'parent_id');
    }
}
