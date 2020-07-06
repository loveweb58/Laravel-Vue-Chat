<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageWasReceived implements ShouldBroadcast
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $user;

    public $data;


    /**
     * Create a new event instance.
     *
     * @param Message $message
     * @param User    $user
     *
     * @internal param $ticket
     */
    public function __construct(Message $message, User $user)
    {
        $message->refresh();
        $text = e($message->text);
        if ($message->mms != '') {
            $text = '<img src="' . e($message->mms) . '" width="300"/>' . '<br>' . e($message->text);
        }
        $contact                           = $user->account->searchContact($message->sender);
        $this->message                     = $message;
        $this->user                        = $user;
        $conversation                      = new \stdClass();
        $conversation->id                  = sha1($message->sender);
        $conversation->name                = $user->account->numberToContact($message->sender);
        $conversation->lastUpdate          = time();
        $conversation->unreadMessagesCount = 1;
        $conversation->type                = "single";
        $conversation->users               = [
            [
                "first_name" => $contact->first_name,
                "last_name"  => $contact->last_name,
                'id'         => $contact->phone,
            ],
        ];
        $vue_message                       = clone $message;
        $vue_message->type                 = "single";
        $vue_message->numbers              = $message->sender;
        $vue_message->date                 = $message->created_at->toDateTimeString();
        $vue_message->sortOrder            = $message->created_at->timestamp;
        $vue_message->name                 = $user->account->numberToContact($message->sender);
        $this->data                        = [
            'avatar'       => e($contact->avatar),
            'chat_user'    => $user->account->numberToContact($message->sender),
            'text'         => $text,
            'conversation' => $conversation,
            'vue_message'  => $vue_message,
        ];
    }


    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'data'    => $this->data,
        ];
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->user->id);
    }
}
