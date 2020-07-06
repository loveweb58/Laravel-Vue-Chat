<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessage;
use App\Models\Did;
use App\Models\Message;
use Auth;
use Illuminate\Http\Request;

class SmsController extends Controller
{

    public function send(Request $request)
    {
        $this->validate($request, [
            'receivers'           => 'required|array|min:1',
            'receivers.*'         => 'required|string|min:1|max:11',
            'text'                => 'nullable|string',
            'sender'              => 'nullable|string',
            'mms'                 => 'nullable|url',
            'payload'             => 'nullable|array',
            'payload.deliveryURL' => 'nullable|url',
        ]);

        if ( ! Auth::user()->account->limits('long_messaging', false)) {
            $this->validate($request, ['text' => 'nullable|string|max:160',]);
        }

        if ( ! Auth::user()->account->limits('api', false)) {
            return response()->api(['errors' => ['account' => 'You don\'t have permission to use API']], 401);
        }

        $mms = [];
        if ($request->has('mms') && Auth::user()->account->limits('mms', false)) {
            $mms = ['mms' => $request->input('mms')];
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
            return response()->api(['errors' => ['sender' => 'You don\'t have did']], 422);
        }

        $segments = 0;

        foreach ($request->input('receivers', []) as $receiver) {

            $uContact = Auth::user()->account->searchContact($receiver);

            /**
             * @var $message Message
             */
            $message = Auth::user()->messages()->create(array_merge($mms, [
                'sender'    => $sender->did,
                'receiver'  => $receiver,
                'text'      => __($request->input('text'), [
                    'first_name' => $uContact->first_name,
                    'last_name'  => $uContact->last_name,
                ]),
                'direction' => 'outbound',
                'folder'    => 'api',
                'status'    => 'pending',
                'payload'   => $request->input('payload'),
            ]));
            dispatch((new SendMessage($message)));
            $messages[] = collect($message)->only([
                'id',
                'sender',
                'receiver',
                'payload',
                'text',
                'segments',
                'status',
                'data',
            ]);
            $segments   += $message->segments;
        }

        return response()->api([
            'data' => [
                'messages'      => $messages,
                'totalMessages' => $segments,
                'failed'        => [],
            ],
        ]);
    }


    /**
     * Create the response for when a request fails validation.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array                    $errors
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        return response()->api(['errors' => $errors], 422);
    }
}
