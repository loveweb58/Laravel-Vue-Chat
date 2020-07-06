<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Conversation
 *
 * @property int                      $id
 * @property int                      $account_id
 * @property string|null              $hash
 * @property string|null              $name
 * @property array                    $members
 * @property array                    $data
 * @property \Carbon\Carbon|null      $created_at
 * @property \Carbon\Carbon|null      $updated_at
 * @property string|null              $deleted_at
 * @property-read \App\Models\Account $account
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conversation onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conversation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conversation withoutTrashed()
 * @mixin \Eloquent
 */
class Conversation extends Model
{

    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'data'    => 'json',
        'members' => 'array',
    ];


    public function account()
    {
        return $this->belongsTo(Account::class);
    }


    public function setMembersAttribute(array $value)
    {
        sort($value);
        $this->attributes['members'] = json_encode($value);
    }


    public function addMember($member)
    {
        if ( ! isset($this->members['member'])) {
            $members       = $this->members;
            $members[]     = $member;
            $this->members = $members;
        }
    }


    public function removeMember($member)
    {
        $members = $this->members;
        if (($key = array_search($member, $members)) !== false) {
            unset($members[$key]);
            $this->members = $members;
        }
    }


    public function getData($key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }


    public function setData($key, $value, $save = false)
    {
        $data       = $this->getAttributeValue('data');
        $data[$key] = $value;
        $this->data = $data;
        if ($save) {
            $this->save();
        }
    }
}
