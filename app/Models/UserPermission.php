<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserPermission
 *
 * @property int $user_id
 * @property string $permission
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPermission wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserPermission whereUserId($value)
 * @mixin \Eloquent
 */
class UserPermission extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user_permissions";

    public $incrementing = false;

    protected $primaryKey = null;

    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
