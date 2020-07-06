<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Contact
 *
 * @property int $id
 * @property int $account_id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string|null $display_name
 * @property string|null $email
 * @property string|null $website
 * @property string $gender
 * @property string|null $birth_date
 * @property string|null $bd_text
 * @property string $avatar
 * @property string|null $company
 * @property string|null $position
 * @property string|null $country
 * @property string|null $city
 * @property string|null $state
 * @property string|null $address
 * @property string|null $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property array $custom_labels
 * @property-read \App\Models\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereBdText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereCustomLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contact whereWebsite($value)
 * @mixin \Eloquent
 */
class Contact extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = ['custom_labels' => 'json'];


    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
