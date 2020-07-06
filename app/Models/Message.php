<?php

namespace App\Models;

use App\MyPhone\Sms\Helpers\SmsLengthCalculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Message
 *
 * @property int                                $id
 * @property string|null                        $group_id
 * @property int                                $account_id
 * @property int|null                           $conversation_id
 * @property string                             $sender
 * @property string                             $receiver
 * @property string|null                        $number
 * @property string|null                        $text
 * @property string|null                        $mms
 * @property string                             $direction
 * @property string                             $folder
 * @property string                             $status
 * @property int                                $segments
 * @property int                                $unread
 * @property array                              $data
 * @property \Carbon\Carbon|null                $created_at
 * @property \Carbon\Carbon|null                $updated_at
 * @property array                              $payload
 * @property-read \App\Models\Account           $account
 * @property-read \App\Models\Conversation|null $conversation
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereMms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereReceiver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereSegments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUnread($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = ['data' => 'json', 'payload' => 'json'];

    protected $keyType = "string";


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        Model::saving(function (Message $message) {
            $calculator        = new SmsLengthCalculator();
            $message->segments = $calculator->getPartCount($message->text);
        });

        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('folder', '!=', 'deleted');
        });
    }


    public function account()
    {
        return $this->belongsTo(Account::class);
    }


    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
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
