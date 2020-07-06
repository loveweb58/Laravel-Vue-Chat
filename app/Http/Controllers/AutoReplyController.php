<?php

namespace App\Http\Controllers;

use App\Models\AutoReply;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AutoReplyController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(AutoReply::class, 'reply');
    }


    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', AutoReply::class);
        $replies = Auth::user()->account->autoReplies()->whereNull('parent_id')->orderBy('order', 'asc')->get();

        $did = Auth::user()->account->did;

        $weekdays = [
            "w_" . Carbon::MONDAY    => 'Monday',
            "w_" . Carbon::TUESDAY   => 'Tuesday',
            "w_" . Carbon::WEDNESDAY => 'Wednesday',
            "w_" . Carbon::THURSDAY  => 'Thursday',
            "w_" . Carbon::FRIDAY    => 'Friday',
            "w_" . Carbon::SATURDAY  => 'Saturday',
            "w_" . Carbon::SUNDAY    => 'Sunday',
        ];

        return view('pages.auto_reply', compact('replies', 'did', 'weekdays'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'did_id'          => [
                'required',
                Rule::exists('did', 'id')->where('account_id', $request->user()->account_id),
            ],
            'parent_id'       => [
                'nullable',
                Rule::exists('auto_reply', 'id')->where('account_id', $request->user()->account_id),
            ],
            'source'          => 'required|string',
            'keyword'         => 'nullable|string',
            'text'            => 'nullable|string',
            'info'            => 'nullable|string',
            'mms'             => 'nullable|image|max:10240',
            'mms_url'         => 'nullable|url',
            'date'            => 'nullable|array',
            'date.date'       => 'nullable|date_format:Y-m-d',
            'date.from'       => 'nullable|date_format:H:i',
            'date.to'         => 'nullable|date_format:H:i',
            'weekdays'        => 'nullable|array',
            'weekdays.*.from' => 'nullable|date_format:H:i',
            'weekdays.*.to'   => 'nullable|date_format:H:i',
            'action'          => 'nullable|in:update_first_name,update_last_name,update_name,schedule,appointment_register,appointment_cancel',
        ]);

        if (Auth::user()->account->autoReplies()->count() >= Auth::user()->account->limits('keywords')) {
            return response(['message' => 'Keywords limit reached'], 500);
        }

        if ( ! Auth::user()->account->limits('long_messaging', false)) {
            $this->validate($request, ['text' => 'nullable|string|max:160',]);
        }

        if ($request->hasFile('mms') && Auth::user()->account->limits('mms', false)) {
            $mms = [
                'mms' => url('storage/' . request()
                        ->file('mms')
                        ->storePublicly("accounts/{$request->user()->account_id}/mms", 'public')),
            ];
        } elseif ($request->has('mms_url') && Auth::user()->account->limits('mms', false)) {
            $mms = ['mms' => $request->input('mms_url')];
        } else {
            $mms = [];
        }

        if ($request->has('keyword')) {
            $request->merge(['keyword' => snake_case($request->input('keyword'))]);
        }

        $weekdays = null;
        foreach ($request->input('weekdays', []) as $k => $v) {
            $weekdays[$k]           = $v;
            $weekdays[$k]['status'] = isset($v['status']);
        }

        Auth::user()->account->autoReplies()->create(array_merge($request->only([
            'did_id',
            'parent_id',
            'source',
            'keyword',
            'text',
            'info',
            'date',
            'action',
        ]), $mms, ['weekdays' => $weekdays, 'order' => (Auth::user()->account->autoReplies()->max('order') + 1)]));

        return response()->json(['message' => 'AutoReply successfully created']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AutoReply $reply
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(AutoReply $reply)
    {
        return response($reply);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\AutoReply    $reply
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, AutoReply $reply)
    {
        $this->validate($request, [
            'did_id'          => [
                'required',
                Rule::exists('did', 'id')->where('account_id', $request->user()->account_id),
            ],
            'source'          => 'required|string',
            'keyword'         => 'nullable|string',
            'text'            => 'nullable|string',
            'info'            => 'nullable|string',
            'mms'             => 'nullable|image|max:10240',
            'mms_url'         => 'nullable|url',
            'date'            => 'nullable|array',
            'date.date'       => 'nullable|date_format:Y-m-d',
            'date.from'       => 'nullable|date_format:H:i',
            'date.to'         => 'nullable|date_format:H:i',
            'weekdays'        => 'nullable|array',
            'weekdays.*.from' => 'nullable|date_format:H:i',
            'weekdays.*.to'   => 'nullable|date_format:H:i',
            'enabled'         => 'required|boolean',
            'action'          => 'nullable|in:update_first_name,update_last_name,update_name,schedule,appointment_register,appointment_cancel',
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
        } elseif ($request->has('mms_url') && Auth::user()->account->limits('mms', false)) {
            $mms = ['mms' => $request->input('mms_url')];
        } else {
            $mms = [];
        }

        if ($request->has('keyword')) {
            $request->merge(['keyword' => snake_case($request->input('keyword'))]);
        }

        $weekdays = null;
        foreach ($request->input('weekdays', []) as $k => $v) {
            $weekdays[$k]           = $v;
            $weekdays[$k]['status'] = isset($v['status']);
        }

        $reply->fill(array_merge($request->only([
            'did_id',
            'source',
            'keyword',
            'text',
            'info',
            'date',
            'enabled',
            'action',
        ]), $mms, ['weekdays' => $weekdays]));

        $reply->save();

        return response()->json(['message' => 'AutoReply successfully updated']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AutoReply $reply
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(AutoReply $reply)
    {
        $reply->delete();

        return redirect('auto-reply')->with('message', 'Auto Reply successfully deleted');
    }


    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|array|min:1',
        ]);
        Auth::user()->account->autoReplies()->whereIn('id', $request->input('id'))->delete();

        return response(['message' => 'Auto Replies successfully deleted']);
    }


    public function sort(Request $request)
    {
        $this->order($request->input('items'), null);
    }


    private function order(array $menuItems, $parentId)
    {
        foreach ($menuItems as $index => $menuItem) {
            /**
             * @var $item AutoReply
             */
            $item            = Auth::user()->account->autoReplies()->find($menuItem['id']);
            $item->order     = $index + 1;
            $item->parent_id = $parentId;
            $item->save();

            if (isset($menuItem['children'])) {
                $this->order($menuItem['children'], $item->id);
            }
        }
    }
}
