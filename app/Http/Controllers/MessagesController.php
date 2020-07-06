<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessage;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Did;
use App\Models\Group;
use App\Models\Message;
use App\Models\ScheduleMessages;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessagesController extends Controller
{

    public function logs()
    {
        return view('pages.logs');
    }


    public function logsData()
    {
        $data = [];

        $query = Auth::user()->account->messages();

        /** @var Builder $query */
        jqxFilters($query, function (Builder $q, $filter) {
            switch ($filter['field']) {
                case 'id':
                    $filter['field'] = "id";
                    break;
            }

            return $filter;
        });

        $data['TotalRows'] = $query->count();

        $query->orderBy(request('sortdatafield', 'id'), request('sortorder', 'desc'))
              ->skip(request('recordstartindex', 0))
              ->take(request('recordendindex', 100));

        $data['Rows'] = $query->get();

        /*$data['Rows'] = collect($data['Rows'])->map(function ($item) {
            $item['sender']   = Auth::user()->account->numberToContact($item['sender']);
            $item['receiver'] = Auth::user()->account->numberToContact($item['receiver']);

            return $item;
        })->toArray();*/

        return response()->json($data);

    }


    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|array|min:1',
        ]);

        if (Auth::user()->can('messages.cancel') && Auth::user()->account->limits('cancel_pending_messages', false)) {
            Auth::user()->account->messages()
                                 ->where('status', 'pending')
                                 ->whereIn('id', $request->input('id'))
                                 ->update(['status' => 'canceled']);
        }

        Auth::user()->account->messages()
                             ->where('folder', 'trashed')
                             ->whereIn('id', $request->input('id'))
                             ->update(['folder' => 'deleted']);

        Auth::user()->account->messages()
                             ->WhereNotIn('folder', ['trashed', 'deleted'])
                             ->whereIn('id', $request->input('id'))
                             ->update(['folder' => 'trashed']);

        return response(['message' => 'Logs successfully deleted']);
    }

    public function send1(Request $request) {
        $id = $request->input('id');
        $schedule = Auth::user()->account->schedule()->where('id',$id)->get(['*']);
        return response()->json($schedule[0]);
    }

    public function groupa(Request $request)
    {

    //    $this->authorize('groupa', Group::class);
    //    $groups = Auth::user()->account->groups()->get(['*']);

    //    return response()->json($groups);
        $contacts = Auth::user()->account->contacts()
                                         ->leftJoin('group_contacts', function (JoinClause $join){
                                             $join->on('group_contacts.contact_id', '=', 'contacts.id');
                                         })
                                         ->leftJoin('groups', function (JoinClause $join){
                                             $join->on('group_id', '=', 'groups.id');
                                         });


        $data = $contacts->get([
            'contacts.*',
            DB::raw('group_id'),
            DB::raw("name as 'group_name'"),
            DB::raw("CONCAT(first_name, ' ' ,last_name) as 'name'"),
        ]);

        return response()->json($data);
    }

    public function send(Request $request)
    {

        $request->merge([
            'receivers' => collect(explode(',', $request->input('receivers', "")))->reject(function ($v, $k) {
                if ($v == "") {
                    return true;
                }

                return false;
            })->toArray(),
            'start_at'  => $request->input('start_at_date', Carbon::now()
                                                                  ->toDateString()) . " " . $request->input('start_at_time', Carbon::now()
                                                                                                                                   ->toTimeString()),
        ]);
        if (Auth::user()->signature != "") {
            $request->merge(['text' => $request->input('text', '') . "\n" . Auth::user()->signature]);
        }
        $this->validate($request, [
            'receivers'   => 'required|array|min:1',
            'receivers.*' => 'required|numeric|digits:11',
            'text'        => 'nullable|string',
            'sender'      => 'nullable|string',
            'mms'         => 'nullable|image|max:10240',
            'mms_url'     => 'nullable|url',
            'start_at'    => 'required|date',
        ]);

        if ( ! Auth::user()->account->limits('long_messaging', false)) {
            $this->validate($request, ['text' => 'nullable|string|max:160',]);
        }

        if ($request->hasFile('mms') ) {
            
            $mms = [
                'mms' => url('storage/' . request()
                        ->file('mms')
                        ->storePublicly("accounts/{$request->user()->account_id}/mms", 'public')),
            ];
        } elseif ($request->has('mms_url') ) {
            $mms = ['mms' => $request->input('mms_url')];
        } else {
            $mms = [];
        }

        $messages = [];

        if ($request->has('sender')) {
            /**
             * @var $sender Did
             */
            $sender = Auth::user()->did()->findOrFail($request->input('sender'));
        } else {
            $sender = Auth::user()->did_sender;
        }

        if ( ! $sender) {
            return response(['message' => 'You don\' have did'], 500);
        }

        if($request->input('is_schedule_hidden',"") == '2') {

            foreach ($request->input('receivers') as $receiver) {

                $uContact = Auth::user()->account->searchContact($receiver);

                /**
                 * @var $message Message
                 */
                $newSchedule = new ScheduleMessages();
                $newSchedule->account_id = 1;
                $newSchedule->sender = $sender->did;
                $newSchedule->receiver = $receiver;
        //        $newSchedule->mms = $mms;
                $newSchedule->mms = $mms['mms'];
                $newSchedule->repeat = $request->input('repeat_times1',"");
                $newSchedule->frequency = $request->input('frequency_type1',"");
                $newSchedule->start_date = '2018-06-07';
                $currentDate = new Carbon;
                $newSchedule->last_date = $currentDate->toDateString();
                $currentDate = $currentDate->subDays(1);
                $newSchedule->end_date = $currentDate->toDateString();
                $newSchedule->start_time = $request->input('schedule_start_at_time1',"");
                $newSchedule->repeat_end = $request->input('repeat_on_date1',"");
                $newSchedule->every = $request->input('every1',"");
                $newSchedule->every_t = $request->input('every_t1',"");
                if($request->input('frequency_type1',"") == 'monthly')
                    $newSchedule->every_t = (new Carbon)->month;
                if($request->input('frequency_type1',"") == 'yearly')
                    $newSchedule->every_t = (new Carbon)->year;
                $newSchedule->dow = $request->input('dow1',"");
                $newSchedule->dom = $request->input('dom1',"");
                $newSchedule->month_weekend_turn = $request->input('monthly_turn1',"");
                $newSchedule->month_weekend_day = $request->input('monthly_day1',"");
                $newSchedule->doy = $request->input('doy1',"");
                $newSchedule->year_weekend_turn = $request->input('yearly_turn1',"");
                $newSchedule->year_weekend_day = $request->input('yearly_day1',"");
                $newSchedule->flag = 1;
                $newSchedule->flagE = 1;

                $text = $request->input('text');
                $newSchedule->text = str_replace(":first_name", $uContact->first_name, $text);
                $newSchedule->save();
            }

            return response()->json(['message' => 'Messages Successfully Scheduled']);
        } else {
            foreach ($request->input('receivers') as $receiver) {

                $uContact = Auth::user()->account->searchContact($receiver);

                /**
                 * @var $message Message
                 */
                $text = $request->input('text');
                $text = str_replace(":first_name", $uContact->first_name, $text);
                
                $message = Auth::user()->messages()->create(array_merge($mms, [
                    'sender'    => $sender->did,
                    'receiver'  => $receiver,
                    'text'      => __($request->input('text'), [
                        'first_name'    => $uContact->first_name,
                        'last_name'     => $uContact->last_name,
                    ]),
                    'direction' => 'outbound',
                    'folder'    => 'chat',
                    'status'    => 'pending',
                ]));
                dispatch((new SendMessage($message))->delay(Carbon::createFromFormat("Y-m-d H:i", $request->input('start_at'))));
                $messages[] = $message;
            }

            if (count($messages) == 1) {
                $text = e($messages[0]->text);
                if ($messages[0]->mms != '') {
                    $text = '<img src="' . $messages[0]->mms . '" width="300"/>' . '<br>' . $messages[0]->text;
                }

                return response()->json([
                    'message' => 'Messages Successfully Send',
                    'id'      => $messages[0]->id,
                    'sender'  => Auth::user()->did_sender->did,
                    'text'    => $text,
                ]);
            }

            return response()->json(['message' => 'Messages Successfully Send']);
        }
    }


    public function archive(Request $request)
    {
        $this->validate($request, [
            'id'                => 'required|string',
            'type'              => 'required|string|in:conversation,message',
            'message_type'      => 'required_if:type,message|string|in:single,group',
            'conversation_type' => 'required_if:type,conversation|string|in:single,conversation',
        ]);

        Auth::user()
            ->messages()
            ->where('folder', 'chat')
            ->when($request->input('type') === "message", function (Builder $q) use ($request) {
                $request->input('message_type') === "single" ? $q->where('id', $request->input('id')) : $q->where('group_id', $request->input('id'));
            }, function (Builder $q) use ($request) {
                $request->input('conversation_type') === "single" ? $q->where(DB::raw("SHA1(number)"), $request->input('id')) : $q->where('conversation_id', $request->input('id'));
            })
            ->update(['folder' => 'archive']);

        return response()->json(['message' => ucfirst($request->input('type')) . ' Successfully Archived']);
    }


    public function sendGroups(Request $request)
    {
        $request->merge([
            'start_at' => $request->input('start_at_date', Carbon::now()
                                                                 ->toDateString()) . " " . $request->input('start_at_time', Carbon::now()
                                                                                                                                  ->toTimeString()),
        ]);

        if (Auth::user()->signature != "") {
            $request->merge(['text' => $request->input('text', '') . "\n" . Auth::user()->signature]);
        }

        $this->validate($request, [
            'group_id' => ['required', Rule::exists('groups', 'id')->where('account_id', Auth::user()->account_id)],
            'text'     => 'nullable|string|max:1000',
            'mms'      => 'nullable|image|max:10240',
            'mms_url'  => 'nullable|url',
            'start_at' => 'required|date',
        ]);

        if ( ! Auth::user()->account->limits('long_messaging', false)) {
            $this->validate($request, ['text' => 'nullable|string|max:160',]);
        }

        if ($request->hasFile('mms') ) {
            $mms = [
                'mms' => url('storage/' . request()
                        ->file('mms')
                        ->storePublicly("accounts/{$request->user()->account_id}/mms", 'public')),
            ];
        } elseif ($request->has('mms_url') ) {
            $mms = ['mms' => $request->input('mms_url')];
        } else {
            $mms = [];
        }


        /**
         * @var $group Group
         */
        $group = Auth::user()->account->groups()->findOrFail($request->input('group_id'));
        /**
         * @var $sender Did
         */
        $sender = Auth::user()->did()->findOrFail($request->input('sender'));

        $receivers = [];

        foreach ($group->contacts as $contact) {
            if ($contact->phone != "") {
                $receivers[] = $contact->phone;
            }
        }

        $conversation = Conversation::where('hash', conversation_hash(Auth::user()->account_id, $receivers))
                                    ->firstOr(function () use ($receivers, $group) {
                                        $conv = (new Conversation)->fill([
                                            'account_id' => Auth::user()->account_id,
                                            'members'    => $receivers,
                                            'name'       => 'Group ' . $group->name,
                                        ]);

                                        $conv->save();

                                        return $conv;
                                    });

        $group_id = uniqid("", true);
        if($request->input('is_schedule_hidden',"") == '2') {

            foreach ($group->contacts as $contact) {
                if ($contact->phone != "") {

                    $uContact = Auth::user()->account->searchContact($contact->phone);

                    /**
                     * @var $message Message
                     */

                    $newSchedule = new ScheduleMessages();
                    $newSchedule->account_id = 1;
                    $newSchedule->sender = $sender->did;
                    $newSchedule->receiver = $contact->phone;
                    $newSchedule->mms = $mms['mms'];
                    $newSchedule->repeat = $request->input('repeat_times1',"");
                    $newSchedule->frequency = $request->input('frequency_type1',"");
                    $newSchedule->start_date = '2018-06-07';
                    $currentDate = new Carbon;
                    $newSchedule->last_date = $currentDate->toDateString();
                    $currentDate = $currentDate->subDays(1);
                    $newSchedule->end_date = $currentDate->toDateString();
                    $newSchedule->start_time = $request->input('schedule_start_at_time1',"");
                    $newSchedule->repeat_end = $request->input('repeat_on_date1',"");
                    $newSchedule->every = $request->input('every1',"");
                    $newSchedule->every_t = $request->input('every_t1',"");
                    if($request->input('frequency_type1',"") == 'monthly')
                        $newSchedule->every_t = (new Carbon)->month;
                    if($request->input('frequency_type1',"") == 'yearly')
                        $newSchedule->every_t = (new Carbon)->year;
                    $newSchedule->dow = $request->input('dow1',"");
                    $newSchedule->dom = $request->input('dom1',"");
                    $newSchedule->month_weekend_turn = $request->input('monthly_turn1',"");
                    $newSchedule->month_weekend_day = $request->input('monthly_day1',"");
                    $newSchedule->doy = $request->input('doy1',"");
                    $newSchedule->year_weekend_turn = $request->input('yearly_turn1',"");
                    $newSchedule->year_weekend_day = $request->input('yearly_day1',"");
                    $newSchedule->flag = 1;
                    $newSchedule->flagE = 1;
                    $text = $request->input('text');
                $newSchedule->text = str_replace(":first_name", $uContact->first_name, $text);
                    $newSchedule->group_id = $group->id;
                    $newSchedule->conversation_id = $conversation->id; 
                    $newSchedule->save();
                }
            }
            return response()->json(['message' => 'Messages Successfully Scheduled']);
        } else {
            foreach ($group->contacts as $contact) {
                if ($contact->phone != "") {

                    $uContact = Auth::user()->account->searchContact($contact->phone);

                    /**
                     * @var $message Message
                     */
                    $text = $request->input('text');
                    $text = str_replace(":first_name", $uContact->first_name, $text);

                    $message = Auth::user()->messages()->create(array_merge($mms, [
                        'conversation_id' => $conversation->id,
                        'group_id'        => $group_id,
                        'sender'          => $sender->did,
                        'receiver'        => $contact->phone,
                        'text'            => $text,
                        'direction'       => 'outbound',
                        'folder'          => 'chat',
                        'status'          => 'pending',
                    ]));
                    dispatch((new SendMessage($message))->delay(Carbon::createFromFormat("Y-m-d H:i", $request->input('start_at'))));
                }
            }
            return response()->json(['message' => 'Messages Successfully Send']);
        }
    }


    public function contact($id)
    {
        $contact = Auth::user()->account->contacts()->where('phone', $id)->first();
        if ($contact) {
            return $contact;
        }

        return ["phone" => $id];
    }

    public function schedule($id)
    {
        $schedule = Auth::user()->account->schedule()->where('id', $id)->first();
        if ($schedule) {
            return $schedule;
        }

        return ["id" => $id];
    }


    public function contactEdit($id, Request $request)
    {
        $this->validate($request, [
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'birth_date'  => 'date|nullable',
            'avatar'      => 'nullable|image',
            'email'       => 'nullable|string|max:255',
            'website'     => 'nullable|string|max:255',
            'gender'      => 'nullable|string|in:M,F',
            'bd_text'     => 'nullable|string|max:255',
            'company'     => 'nullable|string|max:255',
            'position'    => 'nullable|string|max:255',
            'country'     => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:255',
            'state'       => 'nullable|string|max:255',
            'address'     => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        if (request()->hasFile('avatar')) {
            $avatar = url('storage/' . request()
                    ->file('avatar')
                    ->storePublicly("accounts/{$request->user()->account_id}/contacts", 'public'));
        } else {
            $avatar = url("/assets/images/member.jpg");
        }
        Auth::user()->account->contacts()->updateOrCreate(["phone" => $id], array_merge($request->only([
            'first_name',
            'last_name',
            'birth_date',
            'email',
            'website',
            'gender',
            'bd_text',
            'company',
            'position',
            'country',
            'city',
            'state',
            'address',
            'description',
        ]), [
            'avatar' => $avatar,
        ]));

        return response()->json(['message' => 'Contact successfully updated']);
    }

    public function scheduleEdit(Request $request)
    {
        $id = $request->input("sid",'');

        $repeat = $request->input('repeat_times1');
        $frequency = $request->input('frequency_type1');
        $currentDate = new Carbon;
        $currentDate = $currentDate->subDays(1);
        $end_date = $currentDate->toDateString();

        $start_time = $request->input('schedule_start_at_time1');
        $repeat_end = $request->input('repeat_on_date1');


        $every = $request->input('every1');
        $dow = $request->input('dow1');
        $dom = $request->input('dom1');
        $month_weekend_turn = $request->input('monthly_turn1');
        $month_weekend_day = $request->input('monthly_day1');
        $doy = $request->input('doy1');
        $year_weekend_turn = $request->input('yearly_turn1');
        $year_weekend_day = $request->input('yearly_day1');

        $text = $request->input('text');
        if ($request->hasFile('mms') ) {
            
            $mms = [
                'mms' => url('storage/' . request()
                        ->file('mms')
                        ->storePublicly("accounts/{$request->user()->account_id}/mms", 'public')),
            ];
        } elseif ($request->has('mms_url') ) {
            $mms = ['mms' => $request->input('mms_url')];
        } else {
            $mms = [];
        }
        $schedule = Auth::user()->account->schedule()->where('id',$id)->get(['*']);
        $uContact = Auth::user()->account->searchContact($schedule[0]['receiver']);
        $text = str_replace(":first_name", $uContact->first_name, $text);


        ScheduleMessages::where('id',$id)->update(array(
            'text' => $text,
            'repeat' => $repeat,
            'frequency' => $frequency,
            'start_time' => $start_time,
            'repeat_end' => $repeat_end,
            'every' => $every,
            'dow' => $dow,
            'dom' => $dom,
            'end_date' => $end_date,
            'month_weekend_turn' => $month_weekend_turn,
            'month_weekend_day' => $month_weekend_day,
            'doy' => $doy,
            'year_weekend_turn' => $year_weekend_turn,
            'year_weekend_day' => $year_weekend_day,
            'mms' => $mms['mms'],

        ));
        return response()->json(['message' => 'Schedule successfully updated']);
    }

    public function chat()
    {
        $query    = Auth::user()->messages()->where('folder', 'chat')->orderBy('id', 'desc')->take(100);
        $messages = DB::select("SELECT * FROM (" . $query->toSql() . ") AS messages ORDER BY id ASC", $query->getBindings());
        $messages = collect($messages)->map(function ($item) {

            $item = (array)$item;

            if ($item['direction'] == 'inbound') {
                $contact           = Auth::user()->account->searchContact($item['sender']);
                $item['user_id']   = $item['sender'];
                $item['sender']    = Auth::user()->account->numberToContact($item['sender']);
                $item['receiver']  = Auth::user()->first_name . ' ' . Auth::user()->last_name . " (" . $item['receiver'] . ")";
                $item['chat_user'] = $item['sender'];
                $item['avatar']    = e($contact->avatar);
            } else {
                $contact           = Auth::user()->account->searchContact($item['receiver']);
                $item['user_id']   = $item['receiver'];
                $item['sender']    = Auth::user()->first_name . ' ' . Auth::user()->last_name . " (" . $item['sender'] . ")";
                $item['receiver']  = Auth::user()->account->numberToContact($item['receiver']);
                $item['chat_user'] = $item['receiver'];
                $item['avatar']    = e($contact->avatar);
            }
            $item['text'] = e($item['text']);
            if ($item['mms'] != '') {
                $item['text'] = '<img src="' . e($item['mms']) . '" width="300"/>' . '<br>' . $item['text'];
            }
            $item['opponent'] = $item['direction'] == 'inbound' ? true : false;

            return $item;
        })->toArray();

        return response($messages);
    }


    public function lastMessages()
    {
        $messages = Auth::user()
                        ->messages()
                        ->where('direction', 'inbound')
                        ->take(50)
                        ->orderBy('id', 'desc')
                        ->get()
                        ->sortBy(function ($item) {
                            return $item->created_at;
                        })
                        ->map(function ($item) {
                            $item['text'] = e(str_limit($item['text'], 50));
                            if ($item['direction'] == 'inbound') {
                                $item['user_id']   = $item['sender'];
                                $item['sender']    = Auth::user()->account->numberToContact($item['sender']);
                                $item['receiver']  = Auth::user()->first_name . ' ' . Auth::user()->last_name . " (" . $item['receiver'] . ")";
                                $item['chat_user'] = $item['sender'];
                            } else {
                                $item['user_id']   = $item['receiver'];
                                $item['sender']    = Auth::user()->first_name . ' ' . Auth::user()->last_name . " (" . $item['sender'] . ")";
                                $item['receiver']  = Auth::user()->account->numberToContact($item['receiver']);
                                $item['chat_user'] = $item['receiver'];
                            }

                            return $item;
                        })
                        ->values()
                        ->toArray();

        return response()->json($messages);
    }


    public function createConversation(Request $request)
    {
        $this->validate($request, ['users' => 'required|array|min:1', 'users.*.id' => 'required|string']);

        $members = [];
        foreach ($request->input('users') as $user) {
            $members[] = $user['id'];
        }

        $conversation = Conversation::where('hash', conversation_hash(Auth::user()->account_id, $members))
                                    ->firstOr(function () use ($members) {
                                        $conv = (new Conversation)->fill([
                                            'account_id' => Auth::user()->account_id,
                                            'members'    => $members,
                                            'name'       => 'New Conversation ' . (Conversation::max('id') + 1),
                                        ]);

                                        $conv->save();

                                        return $conv;
                                    })
                                    ->toArray();

        foreach ($conversation['members'] as $receiver) {
            if ($receiver) {
                $defaultContact = (new Contact())->fill([
                    'first_name' => null,
                    'last_name'  => null,
                    'phone'      => $receiver,
                    'avatar'     => asset("/assets/images/member.jpg"),
                ]);

                $contact                 = Auth::user()->account->contacts->where('phone', $receiver)
                                                                          ->first() ?? $defaultContact;
                $conversation['users'][] = [
                    'first_name' => $contact->first_name,
                    'last_name'  => $contact->last_name,
                    'avatar'     => $contact->avatar,
                    'id'         => $contact->phone,
                ];
            }
        }
        $conversation['lastUpdate']          = time();
        $conversation['unreadMessagesCount'] = 0;
        $conversation['avatar']              = "/assets/images/member.jpg";
        $conversation['type']                = 'conversation';
        $conversation['id']                  = (string)$conversation['id'];

        return response(['message' => 'Conversation successfully created', 'conversation' => $conversation]);
    }


    public function updateConversation(Request $request)
    {
        $request->merge([
            'users' => collect(explode(',', $request->input('users', "")))->reject(function ($v, $k) {
                if ($v == "") {
                    return true;
                }

                return false;
            })->toArray(),
        ]);
        $this->validate($request, [
            'users'           => 'required|array|min:1',
            'users.*'         => 'required|numeric|digits:11',
            'conversation_id' => 'required|exists:conversations,id',
            'name'            => 'required|string|max:255',
        ]);

        $conversation = Conversation::findOrFail($request->input('conversation_id'));

        $conversation->fill([
            'members' => $request->input('users'),
            'name'    => $request->input('name'),
        ])->save();

        $conversation = $conversation->toArray();

        foreach ($conversation['members'] as $receiver) {
            if ($receiver) {
                $defaultContact = (new Contact())->fill([
                    'first_name' => null,
                    'last_name'  => null,
                    'phone'      => $receiver,
                    'avatar'     => asset("/assets/images/member.jpg"),
                ]);

                $contact                 = Auth::user()->account->contacts->where('phone', $receiver)
                                                                          ->first() ?? $defaultContact;
                $conversation['users'][] = [
                    'first_name' => $contact->first_name,
                    'last_name'  => $contact->last_name,
                    'avatar'     => $contact->avatar,
                    'id'         => $contact->phone,
                ];
            }
            $conversation['id'] = (string)$conversation['id'];
        }

        return response(['message' => 'Conversation successfully updated', 'conversation' => $conversation]);
    }


    public function conversations(Request $request)
    {
        $conversations = DB::table('messages as m')
                           ->select([
                               DB::raw("IF(m.conversation_id IS NULL, SHA1(m.number), m.conversation_id) as id"),
                               DB::raw("IF(m.conversation_id IS NULL, IF(c.display_name IS NULL, m.number, c.display_name), cv.`name`) as `name`"),
                               DB::raw("IF(m.conversation_id IS NULL, IF(c.display_name IS NULL, '/assets/images/member.jpg', c.avatar), '/assets/images/member.jpg') as `avatar`"),
                               DB::raw("IF(m.conversation_id IS NULL, 'single', 'conversation') as type"),
                               DB::raw("UNIX_TIMESTAMP(MAX(m.created_at)) as lastUpdate"),
                               DB::raw("SUM(unread) as 'unreadMessagesCount'"),
                               DB::raw("GROUP_CONCAT(DISTINCT m.number) as 'receivers'"),
                           ])
                           ->leftJoin('contacts as c', function (JoinClause $join) {
                               $join->on('c.phone', '=', 'm.number')
                                    ->on('c.account_id', '=', 'm.account_id')
                                    ->whereRaw("m.conversation_id is null");
                           })
                           ->leftJoin('conversations as cv', function (JoinClause $join) {
                               $join->on('cv.id', '=', 'm.conversation_id')->on('cv.account_id', '=', 'm.account_id');
                           })
                           ->whereIn('m.folder', ['chat'])
                           ->where('m.account_id', '=', Auth::user()->account_id)
                           ->whereIn(DB::raw("IF(direction='inbound',receiver,sender)"), Auth::user()->account->did->pluck('did'))
                           ->groupBy(DB::raw("if( m.conversation_id is null, m.number, m.conversation_id )"))
                           ->orderBy('lastUpdate', 'desc')
                           ->get()
                           ->map(function ($conversation) {
                               $receivers = explode(",", $conversation->receivers);

                               $conversation->users = [];

                               foreach ($receivers as $receiver) {
                                   if ($receiver) {
                                       $defaultContact = (new Contact())->fill([
                                           'first_name' => null,
                                           'last_name'  => null,
                                           'phone'      => $receiver,
                                           'avatar'     => asset("/assets/images/member.jpg"),
                                       ]);

                                       $contact               = Auth::user()->account->contacts->where('phone', $receiver)
                                                                                               ->first() ?? $defaultContact;
                                       $conversation->users[] = [
                                           'first_name' => $contact->first_name,
                                           'last_name'  => $contact->last_name,
                                           'avatar'     => $contact->avatar,
                                           'id'         => $contact->phone,
                                       ];
                                   }
                               }
                               $conversation->lastUpdate          = (int)$conversation->lastUpdate;
                               $conversation->unreadMessagesCount = (int)$conversation->unreadMessagesCount;

                               return $conversation;
                           })
                           ->forPage($request->input('page', 1), 100)
                           ->values()
                           ->all();

        return response($conversations);
    }


    public function markAsRead(Request $request)
    {
        Auth::user()
            ->messages()
            ->whereIn('folder', ['chat'])
            ->when($request->input('type') === "single", function (Builder $q) use ($request) {
                $q->where(DB::raw("SHA1(number)"), $request->input('id'));
            }, function (Builder $q) use ($request) {
                $q->where("conversation_id", $request->input('id'));
            })
            ->update(['unread' => false]);

        return response(['message' => 'Success']);
    }


    public function messages(Request $request)
    {
        Auth::user()
            ->messages()
            ->whereIn('folder', ['chat'])
            ->when($request->input('type') === "single", function (Builder $q) use ($request) {
                $q->where(DB::raw("SHA1(number)"), $request->input('id'))->whereNull('conversation_id');
            }, function (Builder $q) use ($request) {
                $q->where("conversation_id", $request->input('id'));
            })
            ->update(['unread' => false]);

        $messages = Auth::user()
                        ->messages()
                        ->whereIn('folder', ['chat'])
                        ->when($request->input('type') === "single", function (Builder $q) use ($request) {
                            $q->where(DB::raw("SHA1(number)"), $request->input('id'))->whereNull('conversation_id');
                        }, function (Builder $q) use ($request) {
                            $q->where("conversation_id", $request->input('id'));
                        })
                        ->groupBy(DB::raw("IF(group_id IS NULL, id, group_id)"))
                        ->orderBy('sortOrder')
                        ->get([
                            DB::raw("IF(group_id IS NULL, id, group_id) as id"),
                            DB::raw("IF(group_id IS NULL, 'single', 'group') as type"),
                            'text',
                            'mms',
                            'sender',
                            'receiver',
                            'number',
                            'direction',
                            'unread',
                            DB::raw('GROUP_CONCAT(number) as numbers'),
                            DB::raw('MAX(created_at) as date'),
                            DB::raw('UNIX_TIMESTAMP(MAX(created_at)) as sortOrder'),
                        ])
                        ->map(function ($message) {
                            $message->sortOrder = (int)$message->sortOrder;
                            $message->name      = $message->direction === "outbound" ? Auth::user()->first_name . ' ' . Auth::user()->last_name . " (" . $message->sender . ")" : Auth::user()->account->numberToContact($message->sender);

                            return $message;
                        });

        return response($messages);
    }


    public function sendTest(Request $request)
    {
        $this->validate($request, [
            'receivers'      => 'required|array|min:1',
            'receivers.*.id' => 'required|numeric|digits:11',
            'text'           => 'nullable|string',
            'sender'         => 'nullable|string',
            'mms'            => 'nullable|image|max:10240',
        ]);

        if ( ! Auth::user()->account->limits('long_messaging', false)) {
            $this->validate($request, ['text' => 'nullable|string|max:160',]);
        }

        if ($request->hasFile('mms') && Auth::user()->account->limits('mms', false)) {
            $mms = [
                'mms' => url('storage/' . request()
                        ->file('mms')
                        ->storePublicly("accounts/{$request->user()->account_id}/mms", 'public')),
            ];
        } else {
            $mms = [];
        }

        $messages = [];

        if ($request->has('sender')) {
            /**
             * @var $sender Did
             */
            $sender = Auth::user()->did()->where('did', $request->input('sender'))->first();
        } else {
            $sender = Auth::user()->did_sender;
        }

        if ( ! $sender) {
            return response(['message' => 'You don\' have did'], 500);
        }

        if ($request->input('type') === "conversation") {
            $conversation_id = $request->input('conversation_id');
            $group_id        = uniqid('', true);
        }

        foreach ($request->input('receivers') as $receiver) {

            /**
             * @var $message Message
             */
            $message = Auth::user()->messages()->create(array_merge($mms, [
                'conversation_id' => $conversation_id ?? null,
                'group_id'        => $group_id ?? null,
                'sender'          => $sender->did,
                'receiver'        => $receiver['id'],
                'text'            => __($request->input('text'), [
                    'first_name' => $receiver['first_name'] ?? "",
                    'last_name'  => $receiver['last_name'] ?? "",
                ]),
                'direction'       => 'outbound',
                'folder'          => 'chat',
                'status'          => 'pending',
            ]));
            dispatch((new SendMessage($message)));
            $messages[] = $message->id;
        }

        $dbMessages = Auth::user()
                          ->messages()
                          ->whereIn('folder', ['chat'])
                          ->when($request->input('type') === "single", function (Builder $q) use ($request) {
                              $q->where(DB::raw("SHA1(number)"), $request->input('id'));
                          }, function (Builder $q) use ($request) {
                              $q->where("conversation_id", $request->input('conversation_id'));
                          })
                          ->whereIn('id', $messages)
                          ->groupBy(DB::raw("IF(group_id IS NULL, id, group_id)"))
                          ->orderBy('sortOrder')
                          ->get([
                              DB::raw("IF(group_id IS NULL, id, group_id) as id"),
                              DB::raw("IF(group_id IS NULL, 'single', 'group') as type"),
                              'text',
                              'mms',
                              'sender',
                              'receiver',
                              'number',
                              'direction',
                              'unread',
                              DB::raw('GROUP_CONCAT(number) as numbers'),
                              DB::raw('MAX(created_at) as date'),
                              DB::raw('UNIX_TIMESTAMP(MAX(created_at)) as sortOrder'),
                          ])
                          ->map(function ($message) {
                              $message->sortOrder = (int)$message->sortOrder;
                              $message->name      = $message->direction === "outbound" ? Auth::user()->first_name . ' ' . Auth::user()->last_name . " (" . $message->sender . ")" : Auth::user()->account->numberToContact($message->sender);

                              return $message;
                          });

        return response()->json($dbMessages);
    }

}