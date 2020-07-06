<?php

namespace App\Jobs;

use App\Models\Message;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsDeliveryReport implements ShouldQueue
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
            if ( ! isset($this->sms->payload['deliveryURL'])) {
                return;
            }
            $sms    = collect($this->sms)->only([
                'id',
                'sender',
                'receiver',
                'text',
                'mms',
                'status',
                'created_at',
                'updated_at',
                'payload',
                'segments',
                'data',
            ]);
            $client = new Client();
            $client->post($this->sms->payload['deliveryURL'], [
                'json' => $sms,
            ]);
        } catch (\Exception $e) {
            if ($this->attempt < 10) {
                dispatch((new SmsDeliveryReport($this->sms, ($this->attempt + 1)))->delay(Carbon::now()
                                                                                                ->addMinutes(pow(5, $this->attempt))));
            }
        }
    }
}
