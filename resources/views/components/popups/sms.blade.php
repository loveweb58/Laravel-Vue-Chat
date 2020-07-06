<div class="modal fade" id="sms-single">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Send Messages</h4>
            </div>
            <div class="modal-body">
                <form class="validate ajax-form" action="{{url("messages/send")}}" data-callback="clearFields" method="post" enctype="multipart/form-data">
                    <div class="alert alert-success" style="display: none">
                    </div>
                    {{csrf_field()}}
                    <div class="hidden_area">
                        <input type="hidden" name="repeat_times1">
                        <input type="hidden" name="repeat_on_date1">
                        <input type="hidden" name="schedule_start_at_time1">
                        <input type="hidden" name="every1">
                        <input type="hidden" name="every_t1">
                        <input type="hidden" name="frequency_type1">
                        <input type="hidden" name="dow1">
                        <input type="hidden" name="yearly_flag1">
                        <input type="hidden" name="dom1">
                        <input type="hidden" name="monthly_turn1">
                        <input type="hidden" name="monthly_day1">
                        <input type="hidden" name="yearly_flag1">
                        <input type="hidden" name="doy1">
                        <input type="hidden" name="yearly_turn1">
                        <input type="hidden" name="yearly_day1">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="receivers" class="control-label">Receivers</label>
                                <input class="form-control" id="receivers" data-close-select2="true" name="receivers"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sender" class="control-label">Sender</label>
                                <select id="sender" name="sender" class="selectboxit form-control">
                                    @foreach(Auth::user()->did as $n)
                                        @if((Auth::user()->did_sender->id ?? -1) == $n->id)
                                            <option value="{{$n->id}}" selected>{{$n->did}}</option>
                                        @else
                                            <option value="{{$n->id}}">{{$n->did}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_at" class="control-label">Send At(EDT Time)</label>
                                <div class="date-and-time">
                                    <input id="start_at" name="start_at_date" class="form-control datepicker"
                                           data-format="yyyy-mm-dd" value="{{Carbon\Carbon::now()->toDateString()}}"/>
                                    <input type="hidden" id="start_at_time" name="start_at_time" class="form-control timepicker"
                                           data-template="dropdown" data-show-seconds="false" data-show-meridian="false"
                                           data-minute-step="5" value="{{Carbon\Carbon::now()->toTimeString()}}"/>
                                    <input id="start_at_timeP" name="start_at_timeP" class="form-control timepicker"
                                           data-template="dropdown" data-show-seconds="false" data-show-meridian="true"
                                           data-minute-step="5" value="{{Carbon\Carbon::now()->toTimeString()}}"/>
                                </div>
                                @if(Auth::user()->account->limits('recurring_function', false))
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="radio" name="is_schedule" value="once" checked> Once
                                        <input type="hidden" name="is_schedule_hidden" value="1">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="radio" name="is_schedule" value="schedule"> Schedule
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row schedule" style="display: none;color: black;">

                        <div class="row" style="margin-bottom: 5px;">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                end repeat:
                                <select name="schedule_repeat" id="schedule_repeat">
                                    <option>Never</option>
                                    <option>After</option>
                                    <option>On Date</option>
                                </select>
                                <span class="repeat_after" style="display: none;"><input value="1" type="number" style="width: 40px;height: 18px;" name="end_repeat_time" id="end_repeat_time">time(s)</span>
                                <input id="repeat_on_date"  style="display: none;width: 33%;height: 18px;" name="repeat_on_date" class="form-control datepicker"
                                           data-format="yyyy-mm-dd" data-min-date="{{Carbon\Carbon::now()->toDateString()}}" value="{{Carbon\Carbon::now()->toDateString()}}">
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="width: 98%;margin-left: 1%;box-shadow: 0 1px 17px 0 rgb(224, 108, 236), 0 -3px 25px 0 rgb(152, 230, 143);border: solid 1px grey;border-radius: 7px;">
                                <div class="col-md-3 frequency_div">
                                    <input type="radio" name="frequency_type" value="daily" checked>Daily<br>
                                    <input type="radio" name="frequency_type" value="weekly">Weekly<br>
                                    <input type="radio" name="frequency_type" value="monthly">Monthly<br>
                                    <input type="radio" name="frequency_type" value="yearly">Yearly<br>
                                </div>
                                <div class="col-md-6 frequency_tab">
                                    <div class="row daily">
                                        <div class="col-md-12">
                                            Every <input type="number" value="1"  style="width: 20%;height: 20px;" name="daily_period"> day(s)
                                        </div>
                                    </div>
                                    <div class="row weekly" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                Every <input style="width: 20%;height: 20px;" type="number" value="1"  name="weekly_period"> week(s) on:
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="weekly_table enabled_table" >
                                                    <tbody>
                                                        <tr>
                                                            <td>S</td>
                                                            <td>M</td>
                                                            <td>T</td>
                                                            <td>W</td>
                                                            <td>T</td>
                                                            <td>F</td>
                                                            <td>S</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row monthly" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                Every <input style="width: 20%;height: 20px;" type="number" value="1"  name="monthly_period"> month(s)
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="row">
                                                <input type="radio" name="monthly_on" value="each" checked> Each
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="monthly_table enabled_table" >
                                                        <tbody>
                                                            @for($i = 1; $i < 32; $i++ )
                                                                @if($i % 7 == 1)
                                                                    <tr>
                                                                @endif
                                                                <td>{{$i}}</td>
                                                                @if($i % 7 == 0)
                                                                    </tr>
                                                                @endif
                                                            @endfor
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <input type="radio" name="monthly_on" value="on"> On the
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <select name="monthly_turn" id="monthly_turn" disabled="true" style="opacity: 0.3;">
                                                        <option>first</option>
                                                        <option>second</option>
                                                        <option>third</option>
                                                        <option>fourth</option>
                                                        <option>fifth</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select name="monthly_day" id="monthly_day" disabled="true" style="opacity: 0.3;">
                                                        <option>Sunday</option>
                                                        <option>Monday</option>
                                                        <option>Tuesday</option>
                                                        <option>Wednesday</option>
                                                        <option>Thursday</option>
                                                        <option>Friday</option>
                                                        <option>Saturday</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row yearly" style="display: none;">
                                        <div class="row">
                                            Every <input type="number" value="1"  name="yearly_period" style="width: 20%;height: 20px;"> year(s) in:
                                        
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="yearly_table enabled_table">
                                                    <tbody>
                                                        <?php $years = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; ?>
                                                        @for($i = 0; $i < 12; $i++ )
                                                            @if($i % 4 == 0)
                                                                <tr>
                                                            @endif
                                                            <td>{{$years[$i]}}</td>
                                                            @if($i % 4 == 3 )
                                                                </tr>
                                                            @endif
                                                        @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <input type="checkbox" name="yearly_on"> On the:
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select style="opacity: 0.3;" name="yearly_turn" id="yearly_turn" disabled="true">
                                                    <option>first</option>
                                                    <option>second</option>
                                                    <option>third</option>
                                                    <option>fourth</option>
                                                    <option>fifth</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select style="opacity: 0.3;" name="yearly_day" id="yearly_day" disabled="true">
                                                    <option>Sunday</option>
                                                    <option>Monday</option>
                                                    <option>Tuesday</option>
                                                    <option>Wednesday</option>
                                                    <option>Thursday</option>
                                                    <option>Friday</option>
                                                    <option>Saturday</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row button_area">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-11" style="margin-bottom: 10px;">
                                            <button  class="schedule_ok btn btn-info" >Schedule</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 time_area">
                                    <div class="row">
                                        Start time:
                                        <input id="schedule_start_at_timeP" name="schedule_start_at_timeP" class="form-control timepicker"
                                               data-template="dropdown" data-show-meridian="true" value="{{Carbon\Carbon::now()->toDateString()}}"/>
                                        <input type="hidden" id="schedule_start_at_time" name="schedule_start_at_time" class="form-control timepicker" data-template="dropdown" data-show-meridian="true" value="09:00"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="text" class="control-label">0/160 Chars(0 Messages)</label>
                                @if(Auth::user()->account->limits('single_template', false))
                                @if(Auth::user()->account->messageTemplates->count() > 0)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                            Message Templates
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu" data-action="insert_template">
                                            @foreach(Auth::user()->account->messageTemplates as $template)
                                                <li>
                                                    <a href="#" data-text="{{$template->text}}">{{$template->name}}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @endif
                                <textarea name="text" class="form-control" id="text" rows="3" style="resize: vertical"></textarea>
                            </div>
                        </div>
                    </div>
                    @if(  Auth::user()->account->limits('single_mms', false))
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="mms_url" class="control-label">MMS URL</label>
                                    <input name="mms_url" class="form-control" id="mms_url">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div style="vertical-align: middle;line-height: 80px;color: green">OR</div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mms" class="control-label">MMS File
                                        <a href="#" data-toggle="popover" data-trigger="hover" data-placement="top" data-html="true" data-content="Allowed file types: jpg,jpeg,png,gif<br>Max file size: 10 MB" data-original-title="Limits">
                                            <i class="entypo-attention"></i>
                                        </a>
                                    </label>
                                    <input id="mms" type="file" name="mms" accept='.jpeg,.png,.jpg,.gif'>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-info">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="sms-groups">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Send Messages</h4>
            </div>
            <div class="modal-body">
                <form class="validate ajax-form" action="{{url("messages/send-group")}}" data-callback="clearFields" method="post" enctype="multipart/form-data">
                    <div class="alert alert-success" style="display: none">
                    </div>
                    {{csrf_field()}}
                    <div class="hidden_area">
                        <input type="hidden" name="repeat_times1">
                        <input type="hidden" name="repeat_on_date1">
                        <input type="hidden" name="schedule_start_at_time1">
                        <input type="hidden" name="every1">
                        <input type="hidden" name="every_t1">
                        <input type="hidden" name="frequency_type1">
                        <input type="hidden" name="dow1">
                        <input type="hidden" name="yearly_flag1">
                        <input type="hidden" name="dom1">
                        <input type="hidden" name="monthly_turn1">
                        <input type="hidden" name="monthly_day1">
                        <input type="hidden" name="yearly_flag1">
                        <input type="hidden" name="doy1">
                        <input type="hidden" name="yearly_turn1">
                        <input type="hidden" name="yearly_day1">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="group_id" class="control-label">Group</label>
                                <select class="form-control selectboxit" name="group_id" id="group_id">
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sender" class="control-label">Sender</label>
                                <select id="sender" name="sender" class="selectboxit form-control">
                                    @foreach(Auth::user()->did as $n)
                                        @if((Auth::user()->did_sender->id ?? -1) == $n->id)
                                            <option value="{{$n->id}}" selected>{{$n->did}}</option>
                                        @else
                                            <option value="{{$n->id}}">{{$n->did}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_at" class="control-label">Send At(EDT Time)</label>
                                <div class="date-and-time">
                                    <input id="start_at" name="start_at_date" class="form-control datepicker"
                                           data-format="yyyy-mm-dd" value="{{Carbon\Carbon::now()->toDateString()}}">
                                    <input type="hidden" id="start_at_time" name="start_at_time" class="form-control timepicker"
                                           data-template="dropdown" data-show-seconds="false" data-show-meridian="false"
                                           data-minute-step="5" value="{{Carbon\Carbon::now()->toTimeString()}}"/>
                                    <input id="start_at_timeP" name="start_at_timeP" class="form-control timepicker"
                                           data-template="dropdown" data-show-seconds="false" data-show-meridian="true"
                                           data-minute-step="5" value="{{Carbon\Carbon::now()->toTimeString()}}"/>
                                </div>
                                @if(Auth::user()->account->limits('recurring_function', false))
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="radio" name="is_schedule" value="once" checked> Once
                                        <input type="hidden" name="is_schedule_hidden" value="1">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="radio" name="is_schedule" value="schedule"> Schedule
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row schedule" style="display: none;color: black;">

                        <div class="row" style="margin-bottom: 5px;">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                end repeat:
                                <select name="schedule_repeat" id="schedule_repeat">
                                    <option>Never</option>
                                    <option>After</option>
                                    <option>On Date</option>
                                </select>
                                <span class="repeat_after" style="display: none;"><input value="1" type="number" style="width: 40px;height: 18px;" name="end_repeat_time" id="end_repeat_time">time(s)</span>
                                <input id="repeat_on_date"  style="display: none;width: 33%;height: 18px;" name="repeat_on_date" class="form-control datepicker"
                                           data-format="yyyy-mm-dd" data-min-date="{{Carbon\Carbon::now()->toDateString()}}" value="{{Carbon\Carbon::now()->toDateString()}}">
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="width: 98%;margin-left: 1%;box-shadow: 0 1px 17px 0 rgb(224, 108, 236), 0 -3px 25px 0 rgb(152, 230, 143);border: solid 1px grey;border-radius: 7px;">
                                <div class="col-md-3 frequency_div">
                                    <input type="radio" name="frequency_type" value="daily" checked>Daily<br>
                                    <input type="radio" name="frequency_type" value="weekly">Weekly<br>
                                    <input type="radio" name="frequency_type" value="monthly">Monthly<br>
                                    <input type="radio" name="frequency_type" value="yearly">Yearly<br>
                                </div>
                                <div class="col-md-6 frequency_tab">
                                    <div class="row daily">
                                        <div class="col-md-12">
                                            Every <input type="number" value="1"  style="width: 20%;height: 20px;" name="daily_period"> day(s)
                                        </div>
                                    </div>
                                    <div class="row weekly" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                Every <input style="width: 20%;height: 20px;" type="number" value="1"  name="weekly_period"> week(s) on:
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="weekly_table enabled_table" >
                                                    <tbody>
                                                        <tr>
                                                            <td>S</td>
                                                            <td>M</td>
                                                            <td>T</td>
                                                            <td>W</td>
                                                            <td>T</td>
                                                            <td>F</td>
                                                            <td>S</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row monthly" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                Every <input style="width: 20%;height: 20px;" type="number" value="1"  name="monthly_period"> month(s)
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="row">
                                                <input type="radio" name="monthly_on" value="each" checked> Each
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="monthly_table enabled_table" >
                                                        <tbody>
                                                            @for($i = 1; $i < 32; $i++ )
                                                                @if($i % 7 == 1)
                                                                    <tr>
                                                                @endif
                                                                <td>{{$i}}</td>
                                                                @if($i % 7 == 0)
                                                                    </tr>
                                                                @endif
                                                            @endfor
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <input type="radio" name="monthly_on" value="on"> On the
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <select name="monthly_turn" id="monthly_turn" disabled="true" style="opacity: 0.3;">
                                                        <option>first</option>
                                                        <option>second</option>
                                                        <option>third</option>
                                                        <option>fourth</option>
                                                        <option>fifth</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select name="monthly_day" id="monthly_day" disabled="true" style="opacity: 0.3;">
                                                        <option>Sunday</option>
                                                        <option>Monday</option>
                                                        <option>Tuesday</option>
                                                        <option>Wednesday</option>
                                                        <option>Thursday</option>
                                                        <option>Friday</option>
                                                        <option>Saturday</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row yearly" style="display: none;">
                                        <div class="row">
                                            Every <input type="number" value="1"  name="yearly_period" style="width: 20%;height: 20px;"> year(s) in:
                                        
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="yearly_table enabled_table">
                                                    <tbody>
                                                        <?php $years = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; ?>
                                                        @for($i = 0; $i < 12; $i++ )
                                                            @if($i % 4 == 0)
                                                                <tr>
                                                            @endif
                                                            <td>{{$years[$i]}}</td>
                                                            @if($i % 4 == 3 )
                                                                </tr>
                                                            @endif
                                                        @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <input type="checkbox" name="yearly_on"> On the:
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select style="opacity: 0.3;" name="yearly_turn" id="yearly_turn" disabled="true">
                                                    <option>first</option>
                                                    <option>second</option>
                                                    <option>third</option>
                                                    <option>fourth</option>
                                                    <option>fifth</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select style="opacity: 0.3;" name="yearly_day" id="yearly_day" disabled="true">
                                                    <option>Sunday</option>
                                                    <option>Monday</option>
                                                    <option>Tuesday</option>
                                                    <option>Wednesday</option>
                                                    <option>Thursday</option>
                                                    <option>Friday</option>
                                                    <option>Saturday</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row button_area">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-11" style="margin-bottom: 10px;">
                                            <button  class="schedule_ok btn btn-info" >Schedule</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 time_area">
                                    <div class="row">
                                        Start time:
                                        <input id="schedule_start_at_timeP" name="schedule_start_at_timeP" class="form-control timepicker"
                                               data-template="dropdown" data-show-meridian="true" value="{{Carbon\Carbon::now()->toDateString()}}"/>
                                        <input type="hidden" id="schedule_start_at_time" name="schedule_start_at_time" class="form-control timepicker"
                                               data-template="dropdown" data-show-meridian="false" value="09:00"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="text" class="control-label">0/160 Chars(0 Messages)</label>
                                @if(Auth::user()->account->limits('group_template', false))
                                @if(Auth::user()->account->messageTemplates->count() > 0)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                            Message Templates
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu" data-action="insert_template">
                                            @foreach(Auth::user()->account->messageTemplates as $template)
                                                <li>
                                                    <a href="#" data-text="{{$template->text}}">{{$template->name}}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @endif
                                <textarea name="text" class="form-control" id="text" rows="3" style="resize: vertical"></textarea>
                            </div>
                        </div>
                    </div>
                    @if(Auth::user()->account->limits('group_mms', false))
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="mms_url" class="control-label">MMS URL</label>
                                    <input name="mms_url" class="form-control" id="mms_url">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div style="vertical-align: middle;line-height: 80px;color: green">OR</div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mms" class="control-label">MMS File</label>
                                    <input id="mms" type="file" name="mms" accept='.jpeg,.png,.jpg,.gif'>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-info">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="conversation_users">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Conversation Users</h4>
            </div>
            <div class="modal-body">
                <form class="validate ajax-form" action="{{url("messages/conversations")}}" data-callback="updateConversation" method="post" enctype="multipart/form-data">
                    <div class="alert alert-success" style="display: none">
                    </div>
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <input type="hidden" name="conversation_id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name" class="control-label">Conversation Name</label>
                                <input class="form-control" id="name" name="name"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="users" class="control-label">Users</label>
                                <input class="form-control" id="users" data-close-select2="true" name="users"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-info">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>

        function updateConversation(form, data) {
            vueChat.$refs.chat.updateConversation(data.conversation.id, data.conversation);
        }

        $(function () {
            let sms_single = $('#sms-single');

            sms_single.on('hidden.bs.modal', function () {
                if (sms_single.find('form').data('updated')) {
                    window.location.reload(true);
                }
            }).on("keyup", 'textarea[name=text]', function () {
                let length = calculateSmsLength($(this).val());
                let count = sms_single
                    .find("input[name=receivers]")
                    .select2('data').length;
                sms_single.find("label[for=text]")
                          .html(length['chars_used'] + '/' + length['chars_sms'] + ' Chars (' + (length['parts'] * count) + ' Messages)');

            }).on("click",'input[name=is_schedule]',function() {
                if($(this).val() == 'schedule') {
                    sms_single.find("#start_at").prop('disabled',true);
                    sms_single.find("#start_at_timeP").prop('disabled',true);
                    sms_single.find(".schedule").css('display','unset');
                    sms_single.find(".daily").css('display','unset');
                    sms_single.find(".weekly").css('display','none');
                    sms_single.find(".monthly").css('display','none');
                    sms_single.find(".yearly").css('display','none');
                    sms_single.find('.frequency_div input[type=radio]').eq(0).prop("checked",true);
                    sms_single.find('.frequency_div').css('margin-top','0px');
                    sms_single.find('.time_area').css('margin-top','5px');
                    sms_single.find("input[name=is_schedule_hidden]").val('2');
                    sms_single.find(".modal-footer .btn-info").css('display','none');

                } else {
                    sms_single.find("#start_at").prop('disabled',false);
                    sms_single.find("#start_at_timeP").prop('disabled',false);
                    sms_single.find(".schedule").css('display','none');
                    sms_single.find("input[name=is_schedule_hidden]").val('1');
                    sms_single.find(".modal-footer .btn-info").css('display','unset');
                }
            }).on("click",'input[name=frequency_type]',function() {
                let str = '.'+$(this).val();
                if($(this).val() == 'monthly') {
                    sms_single.find('.frequency_div').css('margin-top','53px');
                    sms_single.find('.time_area').css('margin-top','57px');
                }
                else if($(this).val() == 'yearly') {
                    sms_single.find('.frequency_div').css('margin-top','36px');
                    sms_single.find('.time_area').css('margin-top','35px');
                }
                else if($(this).val() == 'weekly') {
                    sms_single.find('.frequency_div').css('margin-top','10px');
                    sms_single.find('.time_area').css('margin-top','5px');
                }
                else {
                    sms_single.find('.frequency_div').css('margin-top','0px');
                    sms_single.find('.time_area').css('margin-top','5px');
                }
                sms_single.find('.daily').css('display','none');
                sms_single.find('.weekly').css('display','none');
                sms_single.find('.monthly').css('display','none');
                sms_single.find('.yearly').css('display','none');
                sms_single.find(str).css('display','unset');
            }).on("click",'input[name=monthly_on]',function() {
                if($(this).val() == 'on') {
                    sms_single.find('.monthly_table').removeClass('enabled_table');
                    sms_single.find('.monthly_table').css('opacity','0.5');
                    sms_single.find('#monthly_turn').prop('disabled',false);
                    sms_single.find('#monthly_day').prop('disabled',false);
                    sms_single.find('#monthly_turn').css('opacity','1');
                    sms_single.find('#monthly_day').css('opacity','1');

                } else {
                    sms_single.find('.monthly_table').addClass('enabled_table');
                    sms_single.find('.monthly_table').css('opacity','1');
                    sms_single.find('#monthly_turn').prop('disabled',true);
                    sms_single.find('#monthly_day').prop('disabled',true);
                    sms_single.find('#monthly_turn').css('opacity','0.3');
                    sms_single.find('#monthly_day').css('opacity','0.3');
                }
            }).on("click",'input[name=yearly_on]',function() {
                if($(this).is(':checked')) {
                    sms_single.find('#yearly_turn').prop('disabled',false);
                    sms_single.find('#yearly_turn').css('opacity','1');
                    sms_single.find('#yearly_day').prop('disabled',false);
                    sms_single.find('#yearly_day').css('opacity','1');
                } else {
                    sms_single.find('#yearly_turn').prop('disabled',true);
                    sms_single.find('#yearly_turn').css('opacity','0.3');
                    sms_single.find('#yearly_day').prop('disabled',true);
                    sms_single.find('#yearly_day').css('opacity','0.3');
                }
            }).on("click",'td',function() {
                if($(this).closest('table').hasClass('enabled_table')) {
                    if($(this).css('background-color') == "rgb(101, 120, 148)") {
                        $(this).css('background-color','#e6e6e6');
                        $(this).removeClass('selected_td');
                    }
                    else {
                        $(this).addClass('selected_td');
                        $(this).css('background-color','#657894');
                    }
                }
            }).on("change",'input[type=number]',function() {
                if($(this).val() < 1) {
                    alert("Input correct number!");
                    $(this).val(1);
                    $(this).focus();
                }
            }).on("change",'#start_at_timeP', function() {
                var str = $(this).val();
                if(str.includes("PM"))
                {
                    str = str.replace(" PM","");
                    var tim = str.split(":");
                    s1 = parseInt(tim[0]) + 12;
                    str = s1 + ":" + tim[1];
                } else {
                    str = str.replace(" AM","");
                }
                sms_single.find('#start_at_time').val(str);
            }).on("change","#schedule_start_at_timeP", function() {
                var str = $(this).val();
                if(str.includes("PM"))
                {
                    str = str.replace(" PM","");
                    var tim = str.split(":");
                    s1 = parseInt(tim[0]) + 12;
                    str = s1 + ":" + tim[1];
                } else {
                    str = str.replace(" AM","");
                }
                sms_single.find('#schedule_start_at_time').val(str);
            }).on('change','.timepicker',function() {
                /*
                var st = parseInt(sms_single.find('#schedule_start_at_time').val().replace(':',''));
                var en = parseInt(sms_single.find('#schedule_end_at_time').val().replace(':',''));
                if(st>en) {
                    if($(this).attr('id') == 'schedule_start_at_time')
                        $(this).val(sms_single.find('#schedule_end_at_time').val());
                    else
                        $(this).val(sms_single.find('#schedule_start_at_time').val());
                    $(this).focus();
                }*/
            }).on('change','#schedule_repeat',function() {
                if($(this).val() == 'Never') {
                    sms_single.find('.repeat_after').css('display','none');
                    sms_single.find('#repeat_on_date').css('display','none');
                } else if($(this).val() == 'After') {
                    sms_single.find('.repeat_after').css('display','unset');
                    sms_single.find('#repeat_on_date').css('display','none');

                } else if($(this).val() == 'On Date') {
                    sms_single.find('.repeat_after').css('display','none');
                    sms_single.find('#repeat_on_date').css('display','unset');
                }
            }).on('click','.schedule_ok',function() {
                let repeat_times = -2;
                let repeat_on_date = "";
                let schedule_start_at_time = sms_single.find('#schedule_start_at_time').val();
                
                let every = 1;
                let frequency_type = sms_single.find('input[name=frequency_type]:checked').val();
                let weekly_period = "";
                let monthly_flag = 0;
                let monthly_period = "";
                let monthly_turn = "";
                let monthly_day = "";

                let yearly_flag = 0;
                let yearly_period = "";
                let yearly_turn = "";
                let yearly_day = "";
                let every_t = 0;
                if(sms_single.find('#schedule_repeat').val() == 'After')
                    repeat_times = sms_single.find('#end_repeat_time').val();
                else if(sms_single.find('#schedule_repeat').val() == 'On Date') {
                    repeat_times = -1;
                    repeat_on_date = sms_single.find('#repeat_on_date').val();
                }
                
                if(frequency_type == 'daily') {
                    every = sms_single.find('input[name=daily_period]').val();
                } else if(frequency_type == 'weekly') {
                    every = sms_single.find('input[name=weekly_period]').val();
                    let i = 0;
                    sms_single.find('.weekly_table td').each(function(index){
                        if($(this).hasClass('selected_td')) {
                            i++;
                            if(i > 1)
                                weekly_period +=',';
                            weekly_period += index;
                            every_t++;
                        }
                    });

                } else if(frequency_type == 'monthly') {
                    every = sms_single.find('input[name=monthly_period]').val();
                    if(sms_single.find('input[name=monthly_on]:checked').val() == 'on')
                        monthly_flag = 1;
                    if(monthly_flag == 0) {
                        let i = 0;
                        sms_single.find('.monthly_table td').each(function(index){
                            if($(this).hasClass('selected_td')) {
                            i++;
                            if(i > 1)
                                monthly_period +=',';
                            monthly_period += (index+1);
                            every_t++;
                            }
                        });
                    } else {
                        monthly_turn = sms_single.find('#monthly_turn').val();
                        monthly_day = sms_single.find('#monthly_day').val();
                    }

                } else if(frequency_type == 'yearly') {
                    every = sms_single.find('input[name=yearly_period]').val();

                    if(sms_single.find('input[name=yearly_on]').prop('checked') == true)
                        yearly_flag = 1;
                    
                    let i = 0;
                    sms_single.find('.yearly_table td').each(function(index){
                        if($(this).hasClass('selected_td')) {
                            i++;
                            if(i > 1)
                                yearly_period +=',';
                            yearly_period += (index+1);
                            every_t++;
                        }
                    });
                    if(yearly_flag == 1){
                        yearly_turn = sms_single.find('#yearly_turn').val();
                        yearly_day = sms_single.find('#yearly_day').val();
                    }
                }

                sms_single.find('input[name=repeat_times1]').val(repeat_times);
                sms_single.find('input[name=repeat_on_date1]').val(repeat_on_date);
                sms_single.find('input[name=schedule_start_at_time1]').val(schedule_start_at_time);
                
                sms_single.find('input[name=every1]').val(every);
                sms_single.find('input[name=every_t1]').val(every_t);
                sms_single.find('input[name=frequency_type1]').val(frequency_type);
                sms_single.find('input[name=dow1]').val(weekly_period);
                sms_single.find('input[name=monthly_flag1]').val(monthly_flag);
                sms_single.find('input[name=dom1]').val(monthly_period);
                sms_single.find('input[name=monthly_turn1]').val(monthly_turn);
                sms_single.find('input[name=monthly_day1]').val(monthly_day);
                sms_single.find('input[name=yearly_flag1]').val(yearly_flag);
                sms_single.find('input[name=doy1]').val(yearly_period);
                sms_single.find('input[name=yearly_turn1]').val(yearly_turn);
                sms_single.find('input[name=yearly_day1]').val(yearly_day);
            });


            let sms_groups = $('#sms-groups');
            sms_groups.on('hidden.bs.modal', function () {
                if (sms_groups.find('form').data('updated')) {
                    window.location.reload(true);
                }
            }).on("keyup", 'textarea[name=text]', function () {
                let length = calculateSmsLength($(this).val());
                sms_groups.find("label[for=text]")
                          .html(length['chars_used'] + '/' + length['chars_sms'] + ' Chars (' + (length['parts'] * 1) + ' Messages)');

//            }).find("[data-action=\"insert_template\"]").find('a').on('click', function () {
//                $(this).closest('form').find('[name="text"]').val($(this).data('text'));
            }).on("click",'input[name=is_schedule]',function() {
                if($(this).val() == 'schedule') {
                    sms_groups.find("#start_at").prop('disabled',true);
                    sms_groups.find("#start_at_time").prop('disabled',true);
                    sms_groups.find(".schedule").css('display','unset');
                    sms_groups.find(".daily").css('display','none');
                    sms_groups.find(".daily").css('display','unset');
                    sms_groups.find("input[name=is_schedule_hidden]").val('2');
                    sms_groups.find(".modal-footer .btn-info").css('display','none');

                } else {
                    sms_groups.find("#start_at").prop('disabled',false);
                    sms_groups.find("#start_at_time").prop('disabled',false);
                    sms_groups.find(".schedule").css('display','none');
                    sms_groups.find("input[name=is_schedule_hidden]").val('1');
                    sms_groups.find(".modal-footer .btn-info").css('display','unset');
                }
            }).on("click",'input[name=frequency_type]',function() {
                let str = '.'+$(this).val();
                if($(this).val() == 'monthly') {
                    sms_groups.find('.frequency_div').css('margin-top','53px');
                    sms_groups.find('.time_area').css('margin-top','57px');
                }
                else if($(this).val() == 'yearly') {
                    sms_groups.find('.frequency_div').css('margin-top','36px');
                    sms_groups.find('.time_area').css('margin-top','35px');
                }
                else if($(this).val() == 'weekly') {
                    sms_groups.find('.frequency_div').css('margin-top','10px');
                    sms_groups.find('.time_area').css('margin-top','5px');
                }
                else {
                    sms_groups.find('.frequency_div').css('margin-top','0px');
                    sms_groups.find('.time_area').css('margin-top','5px');
                }
                sms_groups.find('.daily').css('display','none');
                sms_groups.find('.weekly').css('display','none');
                sms_groups.find('.monthly').css('display','none');
                sms_groups.find('.yearly').css('display','none');
                sms_groups.find(str).css('display','unset');
            }).on("click",'input[name=monthly_on]',function() {
                if($(this).val() == 'on') {
                    sms_groups.find('.monthly_table').removeClass('enabled_table');
                    sms_groups.find('.monthly_table').css('opacity','0.5');
                    sms_groups.find('#monthly_turn').prop('disabled',false);
                    sms_groups.find('#monthly_day').prop('disabled',false);
                    sms_groups.find('#monthly_turn').css('opacity','1');
                    sms_groups.find('#monthly_day').css('opacity','1');

                } else {
                    sms_groups.find('.monthly_table').addClass('enabled_table');
                    sms_groups.find('.monthly_table').css('opacity','1');
                    sms_groups.find('#monthly_turn').prop('disabled',true);
                    sms_groups.find('#monthly_day').prop('disabled',true);
                    sms_groups.find('#monthly_turn').css('opacity','0.3');
                    sms_groups.find('#monthly_day').css('opacity','0.3');
                }
            }).on("click",'input[name=yearly_on]',function() {
                if($(this).is(':checked')) {
                    sms_groups.find('#yearly_turn').prop('disabled',false);
                    sms_groups.find('#yearly_turn').css('opacity','1');
                    sms_groups.find('#yearly_day').prop('disabled',false);
                    sms_groups.find('#yearly_day').css('opacity','1');
                } else {
                    sms_groups.find('#yearly_turn').prop('disabled',true);
                    sms_groups.find('#yearly_turn').css('opacity','0.3');
                    sms_groups.find('#yearly_day').prop('disabled',true);
                    sms_groups.find('#yearly_day').css('opacity','0.3');
                }
            }).on("click",'td',function() {
                if($(this).closest('table').hasClass('enabled_table')) {
                    if($(this).css('background-color') == "rgb(101, 120, 148)") {
                        $(this).css('background-color','#e6e6e6');
                        $(this).removeClass('selected_td');
                    }
                    else {
                        $(this).addClass('selected_td');
                        $(this).css('background-color','#657894');
                    }
                }
            }).on("change",'input[type=number]',function() {
                if($(this).val() < 1) {
                    alert("Input correct number!");
                    $(this).val(1);
                    $(this).focus();
                }
            }).on("change",'#start_at_timeP', function() {
                var str = $(this).val();
                if(str.includes("PM"))
                {
                    str = str.replace(" PM","");
                    var tim = str.split(":");
                    s1 = parseInt(tim[0]) + 12;
                    str = s1 + ":" + tim[1];
                } else {
                    str = str.replace(" AM","");
                }
                sms_groups.find('#start_at_time').val(str);
            }).on("change","#schedule_start_at_timeP", function() {
                var str = $(this).val();
                if(str.includes("PM"))
                {
                    str = str.replace(" PM","");
                    var tim = str.split(":");
                    s1 = parseInt(tim[0]) + 12;
                    str = s1 + ":" + tim[1];
                } else {
                    str = str.replace(" AM","");
                }
                sms_groups.find('#schedule_start_at_time').val(str);
            }).on('change','.timepicker',function() {
            /*    var st = parseInt(sms_groups.find('#schedule_start_at_time').val().replace(':',''));
                var en = parseInt(sms_groups.find('#schedule_end_at_time').val().replace(':',''));
                if(st>en) {
                    if($(this).attr('id') == 'schedule_start_at_time')
                        $(this).val(sms_groups.find('#schedule_end_at_time').val());
                    else
                        $(this).val(sms_groups.find('#schedule_start_at_time').val());
                    $(this).focus();
                }*/
            }).on('change','#schedule_repeat',function() {
                if($(this).val() == 'Never') {
                    sms_groups.find('.repeat_after').css('display','none');
                    sms_groups.find('#repeat_on_date').css('display','none');
                } else if($(this).val() == 'After') {
                    sms_groups.find('.repeat_after').css('display','unset');
                    sms_groups.find('#repeat_on_date').css('display','none');

                } else if($(this).val() == 'On Date') {
                    sms_groups.find('.repeat_after').css('display','none');
                    sms_groups.find('#repeat_on_date').css('display','unset');
                }
            }).on('click','.schedule_ok',function() {
                let repeat_times = -2;
                let repeat_on_date = "";
                let schedule_start_at_time = sms_groups.find('#schedule_start_at_time').val();
                let every = 1;
                let frequency_type = sms_groups.find('input[name=frequency_type]:checked').val();
                let weekly_period = "";
                let monthly_flag = 0;
                let monthly_period = "";
                let monthly_turn = "";
                let monthly_day = "";

                let yearly_flag = 0;
                let yearly_period = "";
                let yearly_turn = "";
                let yearly_day = "";
                let every_t = 0;
                if(sms_groups.find('#schedule_repeat').val() == 'After')
                    repeat_times = sms_groups.find('#end_repeat_time').val();
                else if(sms_groups.find('#schedule_repeat').val() == 'On Date') {
                    repeat_times = -1;
                    repeat_on_date = sms_groups.find('#repeat_on_date').val();
                }
                
                if(frequency_type == 'daily') {
                    every = sms_groups.find('input[name=daily_period]').val();
                } else if(frequency_type == 'weekly') {
                    every = sms_groups.find('input[name=weekly_period]').val();
                    let i = 0;
                    sms_groups.find('.weekly_table td').each(function(index){
                        if($(this).hasClass('selected_td')) {
                            i++;
                            if(i > 1)
                                weekly_period +=',';
                            weekly_period += index;
                            every_t++;
                        }
                    });

                } else if(frequency_type == 'monthly') {
                    every = sms_groups.find('input[name=monthly_period]').val();
                    if(sms_groups.find('input[name=monthly_on]:checked').val() == 'on')
                        monthly_flag = 1;
                    if(monthly_flag == 0) {
                        let i = 0;
                        sms_groups.find('.monthly_table td').each(function(index){
                            if($(this).hasClass('selected_td')) {
                            i++;
                            if(i > 1)
                                monthly_period +=',';
                            monthly_period += (index+1);
                            every_t++;
                            }
                        });
                    } else {
                        monthly_turn = sms_groups.find('#monthly_turn').val();
                        monthly_day = sms_groups.find('#monthly_day').val();
                    }

                } else if(frequency_type == 'yearly') {
                    every = sms_groups.find('input[name=yearly_period]').val();

                    if(sms_groups.find('input[name=yearly_on]').prop('checked') == true)
                        yearly_flag = 1;
                    
                    let i = 0;
                    sms_groups.find('.yearly_table td').each(function(index){
                        if($(this).hasClass('selected_td')) {
                            i++;
                            if(i > 1)
                                yearly_period +=',';
                            yearly_period += (index+1);
                            every_t++;
                        }
                    });
                    if(yearly_flag == 1){
                        yearly_turn = sms_groups.find('#yearly_turn').val();
                        yearly_day = sms_groups.find('#yearly_day').val();
                    }
                }

                sms_groups.find('input[name=repeat_times1]').val(repeat_times);
                sms_groups.find('input[name=repeat_on_date1]').val(repeat_on_date);
                sms_groups.find('input[name=schedule_start_at_time1]').val(schedule_start_at_time);
                sms_groups.find('input[name=every1]').val(every);
                sms_groups.find('input[name=every_t1]').val(every_t);
                sms_groups.find('input[name=frequency_type1]').val(frequency_type);
                sms_groups.find('input[name=dow1]').val(weekly_period);
                sms_groups.find('input[name=monthly_flag1]').val(monthly_flag);
                sms_groups.find('input[name=dom1]').val(monthly_period);
                sms_groups.find('input[name=monthly_turn1]').val(monthly_turn);
                sms_groups.find('input[name=monthly_day1]').val(monthly_day);
                sms_groups.find('input[name=yearly_flag1]').val(yearly_flag);
                sms_groups.find('input[name=doy1]').val(yearly_period);
                sms_groups.find('input[name=yearly_turn1]').val(yearly_turn);
                sms_groups.find('input[name=yearly_day1]').val(yearly_day);
            });

            $("#conversation_users").find('input[name=users]').select2({
                tags: [{!! $contactsJson !!}],
                tokenSeparators: [",", " "],
                formatNoMatches: function () {
                    return "Please input number";
                },
                createSearchChoice: function (input) {
                    input = input.replace(/[^0-9]/g, '');
                    if (input.length > 0 && input.length < 11 && input.substring(0, 1) !== "1") {
                        input = "1" + input;
                    }
                    if (input.length !== 11 || input.substring(0, 1) !== "1") {
                        return null;
                    }
                    return {id: input, text: input};
                }
            });

            sms_single.find('input[name=receivers]').select2({
                tags: [{!! $contactsJson !!}],
                tokenSeparators: [",", " "],
                formatNoMatches: function () {
                    return "Please input number";
                },
                createSearchChoice: function (input) {
                    input = input.replace(/[^0-9]/g, '');
                    if (input.length > 0 && input.length < 11 && input.substring(0, 1) !== "1") {
                        input = "1" + input;
                    }
                    if (input.length !== 11 || input.substring(0, 1) !== "1") {
                        return null;
                    }
                    return {id: input, text: input};
                }
            }).on('change', function () {
                let length = calculateSmsLength(sms_single.find('textarea[name=text]').val());
                let count = sms_single
                    .find("input[name=receivers]")
                    .select2('data').length;
                sms_single.find("label[for=text]")
                          .html(length['chars_used'] + '/' + length['chars_sms'] + ' Chars (' + (length['parts'] * count) + ' Messages)');

            }).end().find("[data-action=\"insert_template\"]").find('a').on('click', function () {
                $(this).closest('form').find('[name="text"]').val($(this).data('text'));
            });
        });
    </script>
@endpush