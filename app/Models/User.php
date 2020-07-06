<?php

namespace App\Models;
use Session;
use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use InvalidArgumentException;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int $account_id
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string|null $api_token
 * @property string $first_name
 * @property string $last_name
 * @property string|null $phone
 * @property string|null $avatar
 * @property string|null $country
 * @property string|null $state
 * @property string|null $city
 * @property string|null $address
 * @property string|null $signature
 * @property int $forward2email
 * @property array $settings
 * @property string|null $ga_secret
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Did[] $did
 * @property-read mixed $did_sender
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserLog[] $logs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserPermission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereForward2email($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereGaSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{

    use Notifiable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected $casts = [
        'settings' => 'json',
    ];


    public function log($text, $payload = [], $type = 'other')
    {
        $this->logs()->create(['type' => $type, 'text' => $text, 'payload' => $payload, 'ip' => request()->ip()]);
    }


    public function logs()
    {
        return $this->hasMany(UserLog::class, 'user_id');
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->where('account_id', $this->account_id);
    }


    public function did()
    {
        return $this->belongsToMany(Did::class, 'user_did')->withPivot(['is_sender']);
    }


    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }


    public function getDidSenderAttribute()
    {
        return $this->did->sortByDesc(function ($did) {
            return $did->pivot->is_sender;
        })->first();
    }


    /**
     * Checks role(s) and permission(s).
     *
     * @param string|array $roles       Array of roles or comma separated string
     * @param string|array $permissions Array of permissions or comma separated string.
     * @param array        $options     validate_all (true|false) or return_type (boolean|array|both)
     *
     * @throws \InvalidArgumentException
     *
     * @return array|bool
     */
    public function ability($roles, $permissions, $options = [])
    {
        // Convert string to array if that's what is passed in.
        if ( ! is_array($roles)) {
            $roles = explode(',', $roles);
        }

        if ( ! is_array($permissions)) {
            $permissions = explode(',', $permissions);
        }

        // Set up default values and validate options.
        if ( ! isset($options['validate_all'])) {
            $options['validate_all'] = false;
        } else {
            if ($options['validate_all'] !== true && $options['validate_all'] !== false) {
                throw new InvalidArgumentException();
            }
        }

        if ( ! isset($options['return_type'])) {
            $options['return_type'] = 'boolean';
        } else {
            if ($options['return_type'] != 'boolean' && $options['return_type'] != 'array' && $options['return_type'] != 'both') {
                throw new InvalidArgumentException();
            }
        }

        // Loop through roles and permissions and check each.
        $checkedRoles       = [];
        $checkedPermissions = [];

        foreach ($roles as $role) {
            $checkedRoles[$role] = $this->hasRole($role);
        }

        foreach ($permissions as $permission) {
            $checkedPermissions[$permission] = $this->can($permission);
        }

        // If validate all and there is a false in either
        // Check that if validate all, then there should not be any false.
        // Check that if not validate all, there must be at least one true.
        if (($options['validate_all'] && ! (in_array(false, $checkedRoles) || in_array(false, $checkedPermissions))) || ( ! $options['validate_all'] && (in_array(true, $checkedRoles) || in_array(true, $checkedPermissions)))) {
            $validateAll = true;
        } else {
            $validateAll = false;
        }

        // Return based on option
        if ($options['return_type'] == 'boolean') {
            return $validateAll;
        } elseif ($options['return_type'] == 'array') {
            return ['roles' => $checkedRoles, 'permissions' => $checkedPermissions];
        } else {
            return [$validateAll, ['roles' => $checkedRoles, 'permissions' => $checkedPermissions]];
        }
    }


    /**
     * Checks if the user has a role by its name.
     *
     * @param string|array $name       Role name or array of role names.
     * @param bool         $requireAll All roles in the array are required.
     *
     * @return bool
     */
    public function hasRole($name, $requireAll = false)
    {
        if (is_array($name)) {
            foreach ($name as $roleName) {
                $hasRole = $this->hasRole($roleName);

                if ($hasRole && ! $requireAll) {
                    return true;
                } elseif ( ! $hasRole && $requireAll) {
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the roles were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the roles were found.
            // Return the value of $requireAll;
            return $requireAll;
        } else {
            foreach ($this->roles as $role) {
                if ($role->name == $name) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Check if user has a permission by its name.
     *
     * @param string|array $permission Permission string or array of permissions.
     * @param bool         $requireAll All permissions in the array are required.
     *
     * @return bool
     */
    public function can($permission, $requireAll = false)
    {
        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $hasPerm = $this->can($permName);

                if ($hasPerm && ! $requireAll) {
                    return true;
                } elseif ( ! $hasPerm && $requireAll) {
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll;
            return $requireAll;
        } else {
            foreach ($this->roles as $role) {
                // Validate against the Permission table
                foreach ($role->permissions as $perm) {
                    if (str_is($permission, $perm->name)) {
                        return true;
                    }
                }
            }
            foreach ($this->permissions as $perm) {
                if (str_is($permission, $perm->permission)) {
                    return true;
                }
            }
        }

        return false;
    }


    public function getSetting($key, $default = null)
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        if ( ! is_null($default)) {
            $this->setSetting($key, $default);
        }

        return $default;
    }


    public function setSetting($key, $value, $save = true)
    {
        $settings       = $this->getAttributeValue('settings');
        $settings[$key] = $value;
        $this->settings = $settings;
        if ($save) {
            $this->save();
        }
    }


    public function account()
    {
        return $this->belongsTo(Account::class);
    }


    public function messages()
    {
        return $this->hasMany(Message::class, 'account_id', 'account_id')
                    ->whereIn(DB::raw("IF(direction='inbound',receiver,sender)"), $this->did->pluck('did'));
    }

    public function generateToken()
    {
        Session::put('api_token',str_random(60));
    //    $this->api_token = str_random(60);
    //    $this->save();
        return session('api_token');
    //    return $this->api_token;
    }
}
