<?php

namespace App\Observers;

use App\Jobs\SmsDeliveryReport;
use App\Jobs\SmsReceive;
use App\Models\Message;

class MessageObserver
{

    public function updating(Message $sms)
    {
        $original = $sms->getOriginal();
        if ($sms->status != $original['status'] && isset($sms->payload['deliveryURL'])) {
            dispatch(new SmsDeliveryReport($sms));
        } elseif ($sms->direction == 'inbound' && $sms->account->setting('api.sms.receive.status', false) && $sms->text != $original['text']) {
            dispatch(new SmsReceive($sms));
        }
    }


    public function created(Message $sms)
    {
        if ($sms->direction == 'inbound' && $sms->account->setting('api.sms.receive.status', false)) {
            dispatch(new SmsReceive($sms));
        }
    }

}