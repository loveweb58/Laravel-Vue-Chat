<?php

namespace App\Jobs;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Sms;

class SendMessage implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;


    /**
     * Create a new job instance.
     *
     * @param Message|Model $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->message->refresh();
            if ($this->message->status == "canceled") {
                return;
            }
            if ($this->message->account->expired()) {
                $this->message->status = 'failed';
                $this->message->setData("reason", "Account Expired");
                $this->message->save();

                return;
            }
            if ($this->message->account->blackList()->where('number', $this->message->receiver)->exists()) {
                $this->message->status = 'failed';
                $this->message->setData("reason", "SMS OFF");
                $this->message->save();

                return;
            }
            if ($this->message->account->messages()
                                       ->whereBetween('created_at', [
                                           Carbon::now()->startOfMonth(),
                                           Carbon::now()->endOfMonth(),
                                       ])
                                       ->whereNull('mms')
                                       ->where('direction', 'outbound')
                                       ->whereNotIn("status", [
                                           'failed',
                                           'canceled',
                                       ])
                                       ->withoutGlobalScope('notDeleted')
                                       ->sum('segments') >= $this->message->account->limits('monthly_sms')) {
                $this->message->status = 'failed';
                $this->message->setData("reason", "SMS Monthly Limit Reached");
                $this->message->save();

                return;
            }
            if ($this->message->mms && $this->message->account->messages()
                                                              ->whereBetween('created_at', [
                                                                  Carbon::now()->startOfMonth(),
                                                                  Carbon::now()->endOfMonth(),
                                                              ])
                                                              ->whereNotNull('mms')
                                                              ->whereNotIn("status", [
                                                                  'failed',
                                                                  'canceled',
                                                              ])
                                                              ->where('direction', 'outbound')
                                                              ->withoutGlobalScope('notDeleted')
                                                              ->sum('segments') >= $this->message->account->limits('monthly_mms')) {
                $this->message->status = 'failed';
                $this->message->setData("reason", "MMS Monthly Limit Reached");
                $this->message->save();

                return;
            }
            Sms::with('AeriaLink')->send([$this->message], $this->message->text, $this->message->sender, []);
            $this->message->status = 'sent';
            $this->message->save();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $this->message->status = 'failed';
            $this->message->save();
        }
    }
}
