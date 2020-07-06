<?php

namespace App\Console\Commands;

use App\Jobs\SendMessage;
use App\Models\Contact;
use App\Models\Did;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBirthDayMessages extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendBirthDayMessages:birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Birthday Messages';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $contacts = Contact::whereNotNull('phone')
                           ->whereNotNull('bd_text')
                           ->whereMonth('birth_date', Carbon::now()->month)
                           ->whereDay('birth_date', Carbon::now()->day)
                           ->get();
        foreach ($contacts as $contact) {
            /**
             * @var $did Did
             */
            $did = $contact->account->did()->first();
            if ($did) {
                $replaces = [];
                foreach ($contact->toArray() as $k => $v) {
                    if (is_string($v)) {
                        $replaces[$k] = $v;
                    }
                }
                dispatch((new SendMessage(Message::create([
                    'account_id' => $contact->account_id,
                    'sender'     => $did->did,
                    'receiver'   => $contact->phone,
                    'text'       => __($contact->bd_text, $replaces),
                    'direction'  => 'outbound',
                    'status'     => 'pending',
                    'folder'     => 'chat',
                ]))));
            }
        }
    }
}
