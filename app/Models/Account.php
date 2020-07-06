<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Account
 *
 * @property int $id
 * @property int $package_id
 * @property string $name
 * @property array $limits
 * @property float $extra_monthly_fee
 * @property \Carbon\Carbon $expired_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property array $settings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Appointment[] $appointments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AutoReply[] $autoReplies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Blacklist[] $blackList
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Contact[] $contacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Conversation[] $conversations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomLabel[] $customLabels
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Did[] $did
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Forward[] $forwards
 * @property-read mixed $monthly_fee
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $groups
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MessageTemplate[] $messageTemplates
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @property-read \App\Models\Package $package
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereExtraMonthlyFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereLimits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $dates = ['expired_at'];

    protected $casts = ['limits' => 'json', 'settings' => 'json',];


    public function limits($type, $default = 0)
    {
        $account = $this->limits[$type] ?? $default;
        $package = $this->package->limits($type, $default);

        if (is_bool($default)) {
            return filter_var($account, FILTER_VALIDATE_BOOLEAN) || filter_var($package, FILTER_VALIDATE_BOOLEAN);
        } elseif (is_numeric($default)) {
            if ($account === $default && $package === $default) {
                return $default;
            } elseif ( ! isset($this->limits[$type])) {
                return $package;
            } elseif ( ! isset($this->package->limits[$type])) {
                return $account;
            }

            return $account + $package;
        } elseif (is_array($default)) {
            return array_merge($account, $package);
        }

        return $account == $default ? $package : $account;

    }

    public function limits1($type, $default = 0)
    {
        $account = $this->limits[$type] ?? $default;
        $package = $this->package->limits($type, $default);
        return $account;
    }


    public function expired()
    {
        return Carbon::now()->greaterThanOrEqualTo($this->expired_at);
    }


    public function getMonthlyFeeAttribute()
    {
        return $this->extra_monthly_fee + $this->package->monthly_fee;
    }


    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function schedule()
    {
        return $this->hasMany(ScheduleMessages::class);
    }


    public function numberToContact($number)
    {
        $contact = $this->contacts->where('phone', $number)->first();
        if ($contact) {
            return $contact->first_name . ' ' . $contact->last_name . " ($number)";
        }

        return $number;
    }


    /**
     * @param $number
     *
     * @return Contact
     */
    public function searchContact($number)
    {
        $contact = $this->contacts->where('phone', $number)->first();
        if ($contact) {
            return $contact;
        }

        return (new Contact())->fill([
            'first_name' => $number,
            'last_name'  => '',
            'phone'      => $number,
            'avatar'     => asset("/assets/images/member.jpg"),
        ]);
    }


    public function package()
    {
        return $this->belongsTo(Package::class);
    }


    public function groups()
    {
        return $this->hasMany(Group::class);
    }


    public function roles()
    {
        return $this->hasMany(Role::class);
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }


    public function did()
    {
        return $this->hasMany(Did::class);
    }


    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }


    public function forwards()
    {
        return $this->hasMany(Forward::class);
    }


    public function autoReplies()
    {
        return $this->hasMany(AutoReply::class);
    }


    public function messages()
    {
        return $this->hasMany(Message::class);
    }


    public function messagesSingle()
    {
        return $this->hasMany(Message::class)
                    ->where('group_id',null);
    }


    public function messagesGroup()
    {
        return $this->hasMany(Message::class)
                    ->whereNotNull('group_id')
                    ->groupBy('group_id');
    }


    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }


    public function customLabels()
    {
        return $this->hasMany(CustomLabel::class);
    }


    public function messageTemplates()
    {
        return $this->hasMany(MessageTemplate::class);
    }


    public function blackList()
    {
        return $this->hasMany(Blacklist::class, 'account_id');
    }


    public function setting($key, $default = null)
    {
        if (is_array($key)) {
            $settings = $this->settings ?? [];
            foreach ($key as $k => $v) {
                array_set($settings, $k, $v);
            }
            $this->settings = $settings;

            return $this;
        }

        return array_dot(is_array($this->settings) ? $this->settings : [])[$key] ?? $default;
    }

}
