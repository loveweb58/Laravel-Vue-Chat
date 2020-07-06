<?php

namespace App\Console\Commands;

use App\Jobs\SendMessage;
use App\Models\Contact;
use App\Models\ScheduleMessages;
use App\Models\Did;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduleMessages extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendScheduleMessages:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled Messages';


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
        $s_messages = ScheduleMessages::where('flag',1)
                            ->where('flagE',1)
                            ->get();
        foreach ($s_messages as $message) {
            /**
             * @var $did Did
             */

            $is = 0; // time correct and another day
            $startTime = new Carbon($message->start_time);
            $endTime = new Carbon($message->start_time);
            $endTime->addMinutes(30);
            $currentTime = new Carbon;
            $lastDate = new Carbon($message->end_date);
            if( ($startTime->diff(new Carbon)->format('%R') == '+') && ($currentTime->diff($endTime)->format('%R') == '+') ) {

                if($lastDate->day != $currentTime->day){
                    $is = 1;

                    ScheduleMessages::where('id',$message->id)->update(array('end_date' => $currentTime->toDateString()));
                }
            }
            //end_date is to find if different day
            // time correct another day
            if($is == 1){

                $flag = 0; // is ok for sending message
                if($message->frequency == 'daily') {
                    if($message->repeat == -1) { //on Date
                        $endDate = new Carbon($message->repeat_end);
                        if( $endDate->diff(new Carbon)->format('%R') == '+' ) {
                            ScheduleMessages::where('id',$message->id)->update(array('flagE',0));
                        } else {
                            if($message->every_t == 0) {
                                $flag = 1;
                            }
                            $message->every_t++;
                            $message->every_t = $message->every_t % $message->every;
                            ScheduleMessages::where('id',$message->id)->update(array('every_t' => $message->every_t));
                        }
                    } else if($message->repeat == -2) { //Never end repeat

                        if($message->every_t == 0) {
                            $flag = 1;
                        }
                        $message->every_t++;
                        $message->every_t = $message->every_t % $message->every;
                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $message->every_t));
                    } else { //repeat times
                        if($message->repeat == 0)
                            ScheduleMessages::where('id',$message->id)->update(array('flagE',0));
                        else {
                            if($message->every_t == 0) {
                                $flag = 1;
                                $message->repeat --;
                                ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                            }
                            $message->every_t++;
                            $message->every_t = $message->every_t % $message->every;
                            ScheduleMessages::where('id',$message->id)->update(array('every_t' => $message->every_t));
                        }
                    }
                } else if($message->frequency == 'weekly') {
                    if($message->repeat == -1) { //on Date
                        $endDate = new Carbon($message->repeat_end);
                        if( $endDate->diff(new Carbon)->format('%R') == '+' ) {
                            ScheduleMessages::where('id',$message->id)->update(array('flagE',0));
                        } else {
                            $weekArray = explode(',',$message->dow);
                            if(in_array($currentTime->dayOfWeek, $weekArray)) {
                                if($message->every_t == sizeof($weekArray)) {
                                    ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                }

                                $st = new Carbon($message->last_date);
                                $st = $st->addDays(7);
                                if($st->diff(new Carbon)->format('%R') == '-') {
                                    $flag = 1;
                                    $message->every_t --;
                                    ScheduleMessages::where('id',$message->id)->update(array('every_t' => $message->every_t));
                                } else {
                                    $st = new Carbon($message->last_date);
                                    $st = $st->addDays($message->every*7);
                                    if($st->diff(new Carbon)->format('%R') == '-') {
                                        
                                    } else {
                                        $message->every_t = sizeof($weekArray);
                                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $message->every_t));
                                    }
                                }
                            }                            
                        }
                    } else if($message->repeat == -2) { //Never end repeat

                        $weekArray = explode(',',$message->dow);
                        if(in_array($currentTime->dayOfWeek, $weekArray)) {
                            if($message->every_t == sizeof($weekArray)) {
                                ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                            }

                            $st = new Carbon($message->last_date);
                            $st = $st->addDays(7);
                            if($st->diff(new Carbon)->format('%R') == '-') {
                                $flag = 1;
                                $message->every_t --;
                                ScheduleMessages::where('id',$message->id)->update(array('every_t' => $message->every_t));
                            } else {
                                $st = new Carbon($message->last_date);
                                $st = $st->addDays($message->every*7);
                                if($st->diff(new Carbon)->format('%R') == '-') {
                                    
                                } else {
                                    $message->every_t = sizeof($weekArray);
                                    ScheduleMessages::where('id',$message->id)->update(array('every_t' => $message->every_t));
                                }
                            }
                        }  
                    } else { //repeat times
                        if($message->repeat == 0)
                            ScheduleMessages::where('id',$message->id)->update(array('flagE',0));
                        else {
                            $weekArray = explode(',',$message->dow);
                            if(in_array($currentTime->dayOfWeek, $weekArray)) {

                                if($message->every_t == sizeof($weekArray)) {
                                    ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                }

                                $st = new Carbon($message->last_date);
                                $st = $st->addDays(7);
                                if($st->diff(new Carbon)->format('%R') == '-') {
                                    $flag = 1;
                                    $message->repeat --;
                                    ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                                    $message->every_t --;
                                    ScheduleMessages::where('id',$message->id)->update(array('every_t' => $message->every_t));
                                } else {
                                    $st = new Carbon($message->last_date);
                                    $st = $st->addDays($message->every*7);
                                    if($st->diff(new Carbon)->format('%R') == '-') {
                                        
                                    } else {
                                        $message->every_t = sizeof($weekArray);
                                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $message->every_t));
                                    }
                                }
                            }
                        }
                    }
                } else if($message->frequency == 'monthly') {
                    if($message->repeat == -1) { //on Date
                        $endDate = new Carbon($message->repeat_end);
                        if( $endDate->diff(new Carbon)->format('%R') == '+' ) {
                            ScheduleMessages::where('id',$message->id)->update(array('flagE',0));
                        } else {
                            if($message->month_weekend_turn == "") {
                                $monthArray = explode(',',$message->dom);
                                if(in_array($currentTime->day, $monthArray)) {
                                    $last_date = new Carbon($message->last_date);
                                    $next = $last_date->addMonths($message->every);
                                    if( $currentTime->month == $message->every_t ) {
                                        $flag = 1;
                                    } else if($next->month == $currentTime->month){
                                        $flag =1;
                                        ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->month));
                                    }
                                }
  
                            } else {
                                $str = $message->month_weekend_turn.' '.$message->month_weekend_day.' of this month';
                                $theday = new Carbon($str);
                                $last_date = new Carbon($message->last_date);
                                $next = $last_date->addMonths($message->every);
                                if($currentTime->toDateString() == $theday->toDateString()){
                                    if( $currentTime->month == $message->every_t ) {
                                        $flag = 1;
                                    } else if($next->month == $currentTime->month){
                                        $flag = 1;
                                        ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->month));
                                    }
                                }
                            }
                            
                            
                        }
                    } else if($message->repeat == -2) { //Never end repeat

                        if($message->month_weekend_turn == "") {
                            $monthArray = explode(',',$message->dom);
                            if(in_array($currentTime->day, $monthArray)) {
                                $last_date = new Carbon($message->last_date);
                                $next = $last_date->addMonths($message->every);
                                if( $currentTime->month == $message->every_t ) {
                                    $flag = 1;
                                } else if($next->month == $currentTime->month){
                                    $flag =1;
                                    ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                    ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->month));
                                }
                            }

                        } else {
                            $str = $message->month_weekend_turn.' '.$message->month_weekend_day.' of this month';
                            $theday = new Carbon($str);
                            $last_date = new Carbon($message->last_date);
                            $next = $last_date->addMonths($message->every);
                            if($currentTime->toDateString() == $theday->toDateString()){
                                if( $currentTime->month == $message->every_t ) {
                                    $flag = 1;
                                } else if($next->month == $currentTime->month){
                                    $flag = 1;
                                    ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                    ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->month));
                                }
                            }
                        }
                    } else { //repeat times
                        if($message->repeat == 0)
                            ScheduleMessages::where('id',$message->id)->update(array('flagE',0));
                        else {
                            if($message->month_weekend_turn == "") {
                                $monthArray = explode(',',$message->dom);
                                if(in_array($currentTime->day, $monthArray)) {
                                    $last_date = new Carbon($message->last_date);
                                    $next = $last_date->addMonths($message->every);
                                    if( $currentTime->month == $message->every_t ) {
                                        $flag = 1;
                                        $message->repeat --;
                                        ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                                    } else if($next->month == $currentTime->month){
                                        $flag =1;
                                        $message->repeat --;
                                        ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                                        ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->month));
                                    }
                                }
  
                            } else {
                                $str = $message->month_weekend_turn.' '.$message->month_weekend_day.' of this month';
                                $theday = new Carbon($str);
                                $last_date = new Carbon($message->last_date);
                                $next = $last_date->addMonths($message->every);
                                if($currentTime->toDateString() == $theday->toDateString()){
                                    if( $currentTime->month == $message->every_t ) {
                                        $flag = 1;
                                        $message->repeat --;
                                        ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                                    } else if($next->month == $currentTime->month){
                                        $flag = 1;
                                        ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->month));
                                        $message->repeat --;
                                        ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                                    }
                                }
                            }
                        }
                    }
                } else { //yearly
                    if($message->repeat == -1) { //on Date
                        $endDate = new Carbon($message->repeat_end);
                        if( $endDate->diff(new Carbon)->format('%R') == '+' ) {
                            ScheduleMessages::where('id',$message->id)->update(array('flagE',0));
                        } else {
                            $yearArray = explode(',',$message->doy);
                            if(in_array($currentTime->month, $yearArray)) {
                                $last_date = new Carbon($message->last_date);
                                $next = $last_date->addYears($message->every);
                                if( $currentTime->year == $message->every_t ) {
                                    if($message->year_weekend_turn == "")
                                        $flag = 1;
                                    else {
                                        $str = $message->year_weekend_turn.' '.$message->year_weekend_day.' of this month';
                                        $theday = new Carbon($str);
                                        if($currentTime->toDateString() == $theday->toDateString()){
                                            $flag = 1;
                                            ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                        }
                                    }
                                } else if($next->year == $currentTime->year){
                                    if($message->year_weekend_turn == "") {
                                        $flag = 1;
                                        ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->year));
                                    }
                                    else {
                                        $str = $message->year_weekend_turn.' '.$message->year_weekend_day.' of this month';
                                        $theday = new Carbon($str);
                                        if($currentTime->toDateString() == $theday->toDateString()){
                                            $flag = 1;
                                            ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                            ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->year));
                                        }
                                    }
                                }
                            }
                            
                        }
                    } else if($message->repeat == -2) { //Never end repeat

                        $yearArray = explode(',',$message->doy);
                        if(in_array($currentTime->month, $yearArray)) {
                            $last_date = new Carbon($message->last_date);
                            $next = $last_date->addYears($message->every);
                            if( $currentTime->year == $message->every_t ) {
                                if($message->year_weekend_turn == "")
                                    $flag = 1;
                                else {
                                    $str = $message->year_weekend_turn.' '.$message->year_weekend_day.' of this month';
                                    $theday = new Carbon($str);
                                    if($currentTime->toDateString() == $theday->toDateString()){
                                        $flag = 1;
                                        ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                    }
                                }
                            } else if($next->year == $currentTime->year){
                                if($message->year_weekend_turn == "") {
                                    $flag = 1;
                                    ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                    ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->year));
                                }
                                else {
                                    $str = $message->year_weekend_turn.' '.$message->year_weekend_day.' of this month';
                                    $theday = new Carbon($str);
                                    if($currentTime->toDateString() == $theday->toDateString()){
                                        $flag = 1;
                                        ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->year));
                                    }
                                }
                            }
                        }
                    } else { //repeat times
                        if($message->repeat == 0)
                            ScheduleMessages::where('id',$message->id)->update(array('flagE',0));
                        else {
                            $yearArray = explode(',',$message->doy);
                            if(in_array($currentTime->month, $yearArray)) {
                                $last_date = new Carbon($message->last_date);
                                $next = $last_date->addYears($message->every);
                                if( $currentTime->year == $message->every_t ) {
                                    if($message->year_weekend_turn == "") {
                                        $flag = 1;
                                        $message->repeat --;
                                        ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                                    }
                                    else {
                                        $str = $message->year_weekend_turn.' '.$message->year_weekend_day.' of this month';
                                        $theday = new Carbon($str);
                                        if($currentTime->toDateString() == $theday->toDateString()){
                                            $flag = 1;
                                            $message->repeat --;
                                            ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                                            ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                        }
                                    }
                                } else if($next->year == $currentTime->year){
                                    if($message->year_weekend_turn == "") {
                                        $flag = 1;
                                        $message->repeat --;
                                        ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                                        ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                        ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->year));
                                    }
                                    else {
                                        $str = $message->year_weekend_turn.' '.$message->year_weekend_day.' of this month';
                                        $theday = new Carbon($str);
                                        if($currentTime->toDateString() == $theday->toDateString()){
                                            $flag = 1;
                                            $message->repeat --;
                                            ScheduleMessages::where('id',$message->id)->update(array('repeat' => $message->repeat));
                                            ScheduleMessages::where('id',$message->id)->update(array('last_date' => $currentTime->toDateString()));
                                            ScheduleMessages::where('id',$message->id)->update(array('every_t' => $currentTime->year));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if($flag == 1) {
                    dispatch((new SendMessage(Message::create([
                        'account_id' => 1,
                        'group_id'   => $message->group_id,
                        'conversation_id'   => $message->conversation_id,
                        'mms'   => $message->mms,
                        'sender'     => $message->sender,
                        'receiver'   => $message->receiver,
                        'text'       => $message->text,
                        'direction'  => 'outbound',
                        'status'     => 'pending',
                        'folder'     => 'chat',
                    ]))));
                }
            }
        }
    }
}
