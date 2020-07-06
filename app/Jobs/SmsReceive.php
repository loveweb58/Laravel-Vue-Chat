<?php

namespace App\Jobs;

use App\Models\Message;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsReceive implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Message
     */
    protected $sms;

    protected $attempt;


    /**
     * Create a new job instance.
     *
     * @param Message    $sms
     * @param            $attempt
     */
    public function __construct(Message $sms, $attempt = 1)
    {
        $this->sms     = $sms;
        $this->attempt = $attempt;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (is_null($this->sms->account->setting('api.sms.receive.url')) || ! $this->sms->account->setting('api.sms.receive.status', false)) {
                return;
            }
            $sms    = collect($this->sms)->only([
                'id',
                'sender',
                'receiver',
                'text',
                'mms',
                'data',
                'status',
                'created_at',
                'updated_at',
                'payload',
                'segments',
            ]);
            $client = new Client();
            $client->post($this->sms->account->setting('api.sms.receive.url'), [
                'json' => $sms,
            ]);
        } catch (\Exception $e) {
            if ($this->attempt < 10) {
                dispatch((new SmsReceive($this->sms, ($this->attempt + 1)))->delay(Carbon::now()
                                                                                         ->addMinutes(pow(5, $this->attempt))));
            }
        }
    }
}
