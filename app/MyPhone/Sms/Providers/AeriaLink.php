<?php

namespace App\MyPhone\Sms\Providers;

use App\Events\MessageWasReceived;
use App\Jobs\SendMessage;
use App\Models\Appointment;
use App\Models\AutoReply;
use App\Models\Contact;
use App\Models\Did;
use App\Models\Forward;
use App\Models\Message;
use App\MyPhone\Sms\Contracts\Provider;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Log;
use Mail;
use Validator;

class AeriaLink extends AbstractProvider implements Provider
{

    protected $client;


    public function __construct(array $config)
    {
        parent::__construct($config);
    }


    public function send(array $messages, $text, $sender, array $parameters = [])
    {
        foreach ($messages as $message) {
            $this->sendRaw($message->sender, $message->receiver, $message->text, $message->mms);
        }

        return true;
    }


    protected function sendRaw($sender, $receiver, $text, $mms = null)
    {
        if (is_null($mms) || $mms == "") {
            $this->getHttpClient()->post($this->config['url'], [
                'form_params' => [
                    'source'             => $sender,
                    'destination'        => $receiver,
                    'messageText'        => $text,
                    'registeredDelivery' => '1',
                ],
                'auth'        => [
                    $this->config['key'],
                    $this->config['secret'],
                ],
            ]);
        } else {
            $this->getHttpClient()->post($this->config['url'], [
                'form_params' => [
                    'source'             => $sender,
                    'destination'        => $receiver,
                    'messageText'        => $text,
                    'mmsURL'             => $mms,
                    'registeredDelivery' => '1',
                ],
                'auth'        => [
                    $this->config['key'],
                    $this->config['secret'],
                ],
            ]);
        }
    }


    /**
     * @param array $parameters
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function receive(array $parameters = [])
    {

        Log::info("mms", request()->all());

        $validator = Validator::make(request()->all(), [
            'source'              => 'required|string',
            'destination'         => 'required|string',
            'messageText '        => 'nullable|string',
            'concatenatedMessage' => 'nullable|string|in:true,false',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag());
        }

        $did = Did::where('did', request('destination'))->firstOrFail();

        $account = $did->account;

        $users = $account->users()->whereHas('did', function (Builder $q) use ($did) {
            $q->where('id', $did->id);
            $q->where('did.account_id', $did->account_id);
        })->get();

        if (request('concatenatedMessage', "false") == 'true') {

            $lastPart = DB::table('msg_parts')
                          ->where('ref_id', request('concatenatedMessageReference'))
                          ->where('destination', request('destination'))
                          ->where('source', request('source'))
                          ->where('total_segments', request('concatenatedMessageSegments'))
                          ->where('created_at', '>=', Carbon::now()->subDay())
                          ->latest()
                          ->first();
            if ($lastPart) {

                DB::table('msg_parts')->insert([
                    'text'           => request('messageText'),
                    'ref_id'         => request('concatenatedMessageReference'),
                    'segment'        => request('concatenatedMessageSegmentNumber'),
                    'total_segments' => request('concatenatedMessageSegments'),
                    'source'         => request('source'),
                    'destination'    => request('destination'),
                    'msg_id'         => $lastPart->msg_id,
                    'created_at'     => Carbon::now(),
                ]);

                $parts = DB::table('msg_parts')
                           ->where('ref_id', request('concatenatedMessageReference'))
                           ->where('destination', request('destination'))
                           ->where('source', request('source'))
                           ->where('total_segments', request('concatenatedMessageSegments'))
                           ->where('created_at', '>=', Carbon::now()->subDay())
                           ->orderBy('segment', 'asc')
                           ->get();

                $text = "";
                foreach ($parts as $part) {
                    $text .= $part->text;
                }

                $message       = Message::find($lastPart->msg_id);
                $message->text = $text;

            } else {
                /**
                 * @var $message Message
                 */
                $message             = new Message();
                $message->account_id = $account->id;
                $message->direction  = 'inbound';
                $message->status     = 'received';
                $message->folder     = 'chat';
                $message->sender     = request('source');
                $message->receiver   = request('destination');
                $message->text       = request('messageText');
                $message->save();
                DB::table('msg_parts')->insert([
                    'text'           => request('messageText'),
                    'ref_id'         => request('concatenatedMessageReference'),
                    'segment'        => request('concatenatedMessageSegmentNumber'),
                    'total_segments' => request('concatenatedMessageSegments'),
                    'source'         => request('source'),
                    'destination'    => request('destination'),
                    'msg_id'         => $message->id,
                    'created_at'     => Carbon::now(),
                ]);
            }

        } else {
            /**
             * @var $message Message
             */
            $message             = new Message();
            $message->account_id = $account->id;
            $message->direction  = 'inbound';
            $message->status     = 'received';
            $message->folder     = 'chat';
            $message->sender     = request('source');
            $message->receiver   = request('destination');
            $message->text       = request('messageText', "null");
        }

        $message->unread = true;

        if (in_array(strtolower($message->text), ['sms off', 'nosms', 'smsoff', 'stop', 'no sms'])) {
            $account->blacklist()->updateOrCreate([
                'number' => $message->sender,
            ], ['deleted_at' => null,]);
        }

        Forward::where('updated_at', '<', Carbon::now()->subMinutes(30)->toDateTimeString())
               ->update(['forward_from' => null]);

        $forwards  = $account->forwards()
                             ->with(['did', 'number'])
                             ->where('enabled', true)
                             ->where('did_id', $did->id)
                             ->where(function (Builder $q) use ($message) {
                                 $q->where('forward_from', $message->sender)->OrwhereNull('forward_from');
                             })
                             ->orderByDesc('forward_from')
                             ->get();
        $receivers = $forwards->groupBy('forward_to');

        foreach ($receivers as $receiver) {
            $fwdExists = $receiver->where('forward_from', $message->sender);
            if ($fwdExists->count() > 0) {
                foreach ($fwdExists as $fwd) {
                    $fwd->updated_at = Carbon::now()->toDateTimeString();
                    $fwd->save();
                    dispatch((new SendMessage($account->messages()->create([
                        'sender'    => $fwd->number->did,
                        'receiver'  => $fwd->forward_to,
                        'text'      => request('messageText', null) . "\nForwarded from : " . $account->numberToContact($message->sender),
                        'direction' => 'outbound',
                        'status'    => 'pending',
                        'folder'    => 'forwards',
                    ]))));
                }
            } else {
                $fwd = $receiver->where('forward_from', null)->first();
                if ($fwd) {
                    $fwd->forward_from = $message->sender;
                    $fwd->save();
                    dispatch((new SendMessage($account->messages()->create([
                        'sender'    => $fwd->number->did,
                        'receiver'  => $fwd->forward_to,
                        'text'      => request('messageText', null) . "\nForwarded from : " . $account->numberToContact($message->sender),
                        'direction' => 'outbound',
                        'status'    => 'pending',
                        'folder'    => 'forwards',
                    ]))));
                }
            }
        }

        $forwards = $account->forwards()
                            ->with(['did', 'number'])
                            ->where('enabled', true)
                            ->where('tmp_did_id', $did->id)
                            ->where('forward_to', $message->sender)
                            ->whereNotNull('forward_from')
                            ->get();

        foreach ($forwards as $fwd) {
            dispatch((new SendMessage($account->messages()->create([
                'sender'    => $fwd->did->did,
                'receiver'  => $fwd->forward_from,
                'text'      => request('messageText', null),
                'direction' => 'outbound',
                'status'    => 'pending',
                'folder'    => 'forwards',
            ]))));
        }

        /**
         * @var $replyMessage Message
         */
        $replyMessage = $account->messages()
                                ->where('folder', 'auto-reply')
                                ->where('created_at', '>=', DB::raw("subdate(now(),interval 5 minute)"))
                                ->where('direction', 'inbound')
                                ->where('sender', $message->sender)
                                ->where('receiver', $message->receiver)
                                ->latest()
                                ->first();

        if ($replyMessage) {
            $oldReply = $account->autoReplies()->where('enabled', 1)->find($replyMessage->data['reply_id'] ?? null);
        } else {
            $oldReply = null;
        }

        $keyword = explode(':', $message->text)[0] ?? "";
        /**
         * @var $autoReply AutoReply
         */
        $autoReply = $account->autoReplies()
                             ->where('enabled', 1)
                             ->where(DB::raw("'$message->sender'"), 'like', DB::raw("REPLACE(REPLACE(source,'*','%'),'?','_')"))
                             ->where('did_id', $did->id)
                             ->where(function (
                                 Builder $q
                             ) use ($message, $keyword) {
                                 $q->whereNull('keyword')->orWhere('keyword', $keyword);
                             })
                             ->where(function (Builder $q) {
                                 $now = Carbon::now();
                                 $q->where(function (Builder $q) use ($now) {
                                     $q->where(DB::raw("CAST(JSON_UNQUOTE(weekdays->\"$.w_" . $now->dayOfWeek . ".from\") AS TIME)"), "<=", $now->toTimeString())
                                       ->where(DB::raw("CAST(JSON_UNQUOTE(weekdays->\"$.w_" . $now->dayOfWeek . ".to\") AS TIME)"), ">=", $now->toTimeString())
                                       ->where(DB::raw("JSON_UNQUOTE(weekdays->\"$.w_" . $now->dayOfWeek . ".status\")"), "true");
                                 })->orWhere(function (Builder $q) use ($now) {
                                     $q->where(DB::raw("CAST(JSON_UNQUOTE(date->\"$.from\") AS TIME)"), "<=", $now->toTimeString())
                                       ->where(DB::raw("CAST(JSON_UNQUOTE(date->\"$.to\") AS TIME)"), ">=", $now->toTimeString())
                                       ->where(DB::raw("CAST(JSON_UNQUOTE(date->\"$.date\") AS DATE)"), "=", $now->toDateString());
                                 })->orWhere(function (Builder $q) use ($now) {
                                     $q->whereNull('date')->whereNull('weekdays');
                                 });
                             })
                             ->orderByRaw("parent_id DESC,`order` ASC")
                             ->when($oldReply, function (Builder $q) use ($oldReply) {
                                 $q->where(function (Builder $q) use ($oldReply) {
                                     $q->where('parent_id', $oldReply->id)->orWhereNull('parent_id');
                                 });
                             }, function (Builder $q) {
                                 $q->whereNull('parent_id');
                             })
                             ->first();

        if ($autoReply) {
            $message->folder = 'auto-reply';
            $message->data   = ['reply_id' => $autoReply->id];

            if (in_array($autoReply->action, ['update_first_name', 'update_last_name', 'update_name'])) {
                /**
                 * @var $contact Contact
                 */
                $contact = $account->contacts()->firstOrCreate(['phone' => $message->sender]);
                switch ($autoReply->action) {
                    case 'update_first_name':
                        $contact->first_name = str_ireplace([$autoReply->keyword, ':'], '', $message->text);
                        break;
                    case 'update_last_name':
                        $contact->last_name = str_ireplace([$autoReply->keyword, ':'], '', $message->text);
                        break;
                    case 'update_name':
                        $contact->first_name = explode(" ", str_ireplace([
                                $autoReply->keyword,
                                ':',
                            ], '', $message->text))[0] ?? "";
                        $contact->last_name  = explode(" ", str_ireplace([
                                $autoReply->keyword,
                                ':',
                            ], '', $message->text))[1] ?? "";
                        break;
                }
                $contact->save();
            }

            $rpContact      = $account->contacts()->where("phone", $message->sender)->first();
            $replaceContact = [];
            if ($rpContact) {
                foreach ($rpContact->toArray() as $k => $v) {
                    if (is_string($v)) {
                        $replaceContact[$k] = $v;
                    }
                }
            }

            if ($autoReply->action == "schedule") {
                $date = str_ireplace([$autoReply->keyword, ':'], '', $message->text);
                if ($date != '' && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
                    $date = Carbon::createFromFormat("Y-m-d", $date)->toDateString();
                } else {
                    $date = Carbon::now()->toDateString();
                }
                $appointments      = $account->appointments()
                                             ->where(DB::raw("date(`date`)"), $date)
                                             ->where('date', '>=', Carbon::now()->toDateTimeString())
                                             ->where('status', 'available')
                                             ->orderBy('date')
                                             ->get();
                $availAppointments = "";
                foreach ($appointments as $appointment) {
                    $availAppointments .= "{$appointment->date->format('H:i')} - $appointment->id, \n";
                }
                $text = __($autoReply->text, ['date' => $date, 'appointments' => $availAppointments]);;
                if (count($appointments) == 0) {
                    $text = $account->setting('appointments.texts.not_available');
                }
                if ($text != "") {
                    dispatch((new SendMessage($account->messages()->create([
                        'sender'    => $message->receiver,
                        'receiver'  => $message->sender,
                        'text'      => __($text, $replaceContact),
                        'direction' => 'outbound',
                        'status'    => 'pending',
                        'folder'    => 'auto-reply',
                    ]))));
                }
            } elseif ($autoReply->action == "appointment_register") {
                $id = str_ireplace([$autoReply->keyword, ':'], '', $message->text);

                if (is_numeric($id)) {

                    /**
                     * @var $appointment Appointment
                     */
                    $appointment = $account->appointments()
                                           ->where('status', 'available')
                                           ->where('date', '>=', Carbon::now()->toDateTimeString())
                                           ->find($id);

                    if ($appointment) {
                        $appointment->number = $message->sender;
                        $appointment->status = 'busy';
                        $appointment->save();
                        dispatch((new SendMessage($account->messages()->create([
                            'sender'    => $message->receiver,
                            'receiver'  => $message->sender,
                            'text'      => __($account->setting('appointments.texts.success'), ['date' => $appointment->date]),
                            'direction' => 'outbound',
                            'status'    => 'pending',
                            'folder'    => 'appointments',
                        ]))));
                        if ($users->count() > 0) {
                            $user = $users->first();
                            if (strlen($user->email) > 0) {
                                try {
                                    Mail::raw("A new appointment has been booked for:$appointment->date from {$account->numberToContact($message->sender)}", function ($message) use ($user) {
                                        $message->subject('New appointment');
                                        $message->to($user->email);
                                    });
                                } catch (\Exception $e) {
                                }
                            }
                            if (strlen($user->phone) > 0) {
                                dispatch((new SendMessage($account->messages()->create([
                                    'sender'    => $message->receiver,
                                    'receiver'  => $user->phone,
                                    'text'      => "A new appointment has been booked for:$appointment->date from {$account->numberToContact($message->sender)}",
                                    'direction' => 'outbound',
                                    'status'    => 'pending',
                                    'folder'    => 'appointments',
                                ]))));
                            }
                        }
                    } else {
                        dispatch((new SendMessage($account->messages()->create([
                            'sender'    => $message->receiver,
                            'receiver'  => $message->sender,
                            'text'      => $account->setting('appointments.texts.not_available'),
                            'direction' => 'outbound',
                            'status'    => 'pending',
                            'folder'    => 'appointments',
                        ]))));
                    }
                } else {
                    dispatch((new SendMessage($account->messages()->create([
                        'sender'    => $message->receiver,
                        'receiver'  => $message->sender,
                        'text'      => $account->setting('appointments.texts.not_available'),
                        'direction' => 'outbound',
                        'status'    => 'pending',
                        'folder'    => 'appointments',
                    ]))));
                }
            } elseif ($autoReply->action == "appointment_cancel") {
                $id = str_ireplace([$autoReply->keyword, ':'], '', $message->text);

                if (is_numeric($id)) {

                    /**
                     * @var $appointment Appointment
                     */
                    $appointment = $account->appointments()
                                           ->where('status', 'busy')
                                           ->where('number', $message->sender)
                                           ->where('date', '>=', Carbon::now()->toDateTimeString())
                                           ->find($id);

                    if ($appointment) {
                        $appointment->status = 'canceled';
                        $appointment->save();
                        dispatch((new SendMessage($account->messages()->create([
                            'sender'    => $message->receiver,
                            'receiver'  => $message->sender,
                            'text'      => $account->setting('appointments.texts.cancel'),
                            'direction' => 'outbound',
                            'status'    => 'pending',
                            'folder'    => 'appointments',
                        ]))));
                        if ($users->count() > 0) {
                            $user = $users->first();
                            if (strlen($user->email) > 0) {
                                try {
                                    Mail::raw("Appointment to $appointment->date from {$account->numberToContact($message->sender)} has been cancelled", function ($message) use ($user) {
                                        $message->from(config('mail.from.address'), config('mail.from.name'));
                                        $message->subject('New appointment');
                                        $message->to($user->email);
                                    });
                                } catch (\Exception $e) {
                                }
                            }
                            if (strlen($user->phone) > 0) {
                                dispatch((new SendMessage($account->messages()->create([
                                    'sender'    => $message->receiver,
                                    'receiver'  => $user->phone,
                                    'text'      => "Appointment to $appointment->date from {$account->numberToContact($message->sender)} has been cancelled",
                                    'direction' => 'outbound',
                                    'status'    => 'pending',
                                    'folder'    => 'appointments',
                                ]))));
                            }
                        }
                    } else {
                        dispatch((new SendMessage($account->messages()->create([
                            'sender'    => $message->receiver,
                            'receiver'  => $message->sender,
                            'text'      => $account->setting('appointments.texts.cancel_error'),
                            'direction' => 'outbound',
                            'status'    => 'pending',
                            'folder'    => 'appointments',
                        ]))));
                    }
                } else {
                    dispatch((new SendMessage($account->messages()->create([
                        'sender'    => $message->receiver,
                        'receiver'  => $message->sender,
                        'text'      => $account->setting('appointments.texts.cancel_error'),
                        'direction' => 'outbound',
                        'status'    => 'pending',
                        'folder'    => 'appointments',
                    ]))));
                }
            } else {
                if ($autoReply->text != "") {
                    dispatch((new SendMessage($account->messages()->create([
                        'sender'    => $message->receiver,
                        'receiver'  => $message->sender,
                        'text'      => __($autoReply->text, $replaceContact),
                        'mms'       => $autoReply->mms,
                        'direction' => 'outbound',
                        'status'    => 'pending',
                        'folder'    => 'auto-reply',
                    ]))));
                }
            }
        }

        try {
            if (request()->has('attachments')) {
                $mms          = json_decode(request('attachments'), JSON_OBJECT_AS_ARRAY | JSON_UNESCAPED_UNICODE)[0] ?? [];
                $message->mms = $mms["contentFile"] ?? null;
            }
        } catch (\Exception $e) {

        }

        $message->save();

        foreach ($users as $user) {
            if ($user->did) {
                if ($message->folder == 'chat') {
                    if ($user->forward2email) {
                        $mailContent = "New Message From\nSender: " . $message->sender . "\nReceiver: " . $message->receiver . "\nText: " . $message->text;
                        try {
                            Mail::raw($mailContent, function ($message) use ($user) {
                                $message->subject('Inbound Message');
                                $message->to($user->email);
                            });
                        } catch (\Exception $e) {
                            Log::warning($e->getMessage());
                        }
                    }
                    broadcast(new MessageWasReceived($message, $user));
                }
            }
        }

        return response()->json(['message' => 'received']);
    }

}