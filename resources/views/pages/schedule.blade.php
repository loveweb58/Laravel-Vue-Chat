@extends('layouts.dashboard', ['current_page'=>'Schedule', 'title'=>'Schedule'])

@section('content')
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div id="jqxgrid_contacts"></div>
        </div>
    </div>
    <div class="modal fade" id="edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Schedule</h4>
                </div>
                <div class="modal-body">
                    <form class="ajax-form" action="{{url('messages/edit')}}" method="post" enctype="multipart/form-data">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        <input type="hidden" name="sid" id="sid">
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

                        <div class="row schedule" style="color: black;">

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
                                <div class="col-md-12" style="width: 94%;margin-left: 3%;box-shadow: 0 1px 17px 0 rgb(224, 108, 236), 0 -3px 25px 0 rgb(152, 230, 143);border: solid 1px grey;border-radius: 7px;">
                                    <div class="col-md-3 frequency_div">
                                        <input type="radio" name="frequency_type" value="daily" checked>Daily<br>
                                        <input type="radio" name="frequency_type" value="weekly">Weekly<br>
                                        <input type="radio" name="frequency_type" value="monthly">Monthly<br>
                                        <input type="radio" name="frequency_type" value="yearly">Yearly<br>
                                    </div>
                                    <div class="col-md-6 frequency_tab">
                                        <div class="row daily" style="margin-top: 20px;">
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
                                    </div>
                                    <div class="col-md-3 time_area" style="margin-top: 5px;">
                                        <div class="row">
                                            Start time:
                                            <input id="schedule_start_at_timeP" name="schedule_start_at_timeP" class="form-control timepicker"
                                                   data-template="dropdown" data-show-meridian="true" value="09:00"/>
                                            <input type="hidden" id="schedule_start_at_time" name="schedule_start_at_time" class="form-control timepicker" data-template="dropdown" data-show-meridian="true" value="09:00"/>
                                        </div>
                                    </div>
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="text" class="control-label">Schedule Text</label>
                                    <textarea id="text" name="text" style="max-width: 100%;" class="form-control"></textarea>
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
@endsection
@push('styles')
    <link rel="stylesheet" href="{{asset('assets/jqwidgets/styles/jqx.base.css')}}" type="text/css"/>
    <link rel="stylesheet" href="{{asset('assets/jqwidgets/styles/jqx.metro.css')}}" type="text/css"/>
    <style>
        .select2-drop {
            z-index: 100000 !important;
        }

        .page-body .select2-container .select2-choice {
            height: 23px;
            line-height: 22px;
            margin: 4px
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxcore.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxbuttons.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxscrollbar.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxmenu.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.selection.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.filter.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.sort.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.columnsresize.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxdata.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.edit.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxlistbox.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxcheckbox.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.pager.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxdropdownlist.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxwindow.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxcalendar.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxdatetimeinput.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxdata.export.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.export.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/globalization/globalize.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/globalization/translations.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var source =
                {
                    datatype: "json",
                    datafields: [
                        {name: 'id', type: 'integer'},
                        {name: 'sender', type: 'string'},
                        {name: 'receiver', type: 'string'},
                        {name: 'group_id', type: 'string'},
                        {name: 'frequency', type: 'string'},
                        {name: 'start_time', type: 'time'},
                        {name: 'repeat_end', type: 'string'},
                        {name: 'dow', type: 'string'},
                        {name: 'dom', type: 'string'},
                        {name: 'month_weekend_turn', type: 'string'},
                        {name: 'month_weekend_day', type: 'string'},
                        {name: 'doy', type: 'string'},
                        {name: 'year_weekend_turn', type: 'string'},
                        {name: 'year_weekend_day', type: 'string'},
                        {name: 'text', type: 'string'}
                    ],
                    cache: false,
                    url: Config.defaultURL + "/schedule/all1",
                    data: {_token: Config.csrfToken},
                    type: 'GET',
                    filter: function () {
                        $("#jqxgrid_contacts").jqxGrid('updatebounddata', 'filter');
                    },
                    sort: function () {
                        $("#jqxgrid_contacts").jqxGrid('updatebounddata', 'sort');
                    },
                    root: 'Rows',
                    beforeprocessing: function (data) {
                        if (data !== null) {
                            source.totalrecords = data.TotalRows;
                        }
                    }
                };
            var dataadapter = new $.jqx.dataAdapter(source, {
                    loadError: function (xhr, status, error) {
                        console.log(error);
                    }
                }
            );

            var contacts = $("#jqxgrid_contacts").jqxGrid(
                {
                    source: dataadapter,
                    filterable: true,
                    width: '100%',
                    height: $(window).height() - 175 < 300 ? 300 : $(window).height() - 175,
                    sortable: true,
                    autoheight: false,
                    pageable: true,
                    virtualmode: true,
                    pagesize: 100,
                    pagesizeoptions: ['10', '50', '100', '500', '1000'],
                    columnsresize: true,
                    showfilterrow: true,
                    altrows: true,
                    enabletooltips: true,
                    enablehover: false,
                    enablebrowserselection: true,
                    editable: false,
                    selectionmode: 'checkbox',
                    theme: 'metro',
                    rendergridrows: function (obj) {
                        return obj.data;
                    },
                    @if(Auth::user()->can(['schedule.view','schedule.delete']))
                    showtoolbar: true,
                    rendertoolbar: function (toolbar) {
                        var container = $("<div style='margin: 5px;'></div>");
                        toolbar.append(container);
                        
                        container.append('<button class="btn btn-danger btn-sm btn-icon icon-left" id="delete_contact" style="margin-left: 10px;display: none"><i class="entypo-trash"></i>Delete</button>');
                        $("#delete_contact").on('click', function () {
                            var del_id = [];
                            $.each(contacts.jqxGrid('getselectedrowindexes'), function (k, v) {
                                var row = contacts.jqxGrid('getrowdata', v);
                                del_id.push(row.id);
                            });
                            BootstrapDialog.confirm({
                                title: 'Recurring Message Delete',
                                message: 'Do you really want to delete this schedule?',
                                type: BootstrapDialog.TYPE_DANGER,
                                closable: true,
                                draggable: false,
                                btnCancelLabel: 'Close',
                                btnOKLabel: 'Delete',
                                btnOKClass: 'btn-danger',
                                callback: function (result) {
                                    if (result) {
                                        $.post(Config.defaultURL + "/schedule", {
                                            _method: 'DELETE',
                                            id: del_id
                                        }, function () {
                                            if (contacts.jqxGrid('getdatainformation').rowscount <= del_id.length) {
                                                contacts.jqxGrid('clear');
                                                $('#delete_contact').hide();
                                            } else {
                                                contacts.jqxGrid('beginupdate');
                                                $.each(contacts.jqxGrid('getselectedrowindexes'), function (k, v) {
                                                    contacts.jqxGrid('deleterow', v);
                                                });
                                                contacts.jqxGrid('endupdate');
                                            }
                                            contacts.jqxGrid('clearselection');
                                        }, "json");
                                    }
                                }
                            });
                        });
                    },
                    @endif
                    columns: [
                        {text: 'Sender', datafield: 'sender', cellsalign: 'center', align: 'center'},
                        {text: 'Receiver', datafield: 'receiver', cellsalign: 'center', align: 'center'},
                        {text: 'GroupId', datafield: 'group_id', cellsalign: 'center', align: 'center'},
                        {text: 'Frequency', datafield: 'frequency', cellsalign: 'center', align: 'center'},
                        {text: 'Send Time', datafield: 'start_time', cellsalign: 'center', align: 'center'},
                        {text: 'Repeat End Date', datafield: 'repeat_end', cellsalign: 'center', align: 'center'},
                        {text: 'Text', datafield: 'dow', cellsalign: 'center', align: 'center',hidden: true},
                        {text: 'Text', datafield: 'dom', cellsalign: 'center', align: 'center',hidden: true},
                        {text: 'Text', datafield: 'month_weekend_turn', cellsalign: 'center', align: 'center',hidden: true},
                        {text: 'Text', datafield: 'month_weekend_day', cellsalign: 'center', align: 'center',hidden: true},

                        {text: 'Text', datafield: 'doy', cellsalign: 'center', align: 'center',hidden: true},
                        {text: 'Text', datafield: 'year_weekend_turn', cellsalign: 'center', align: 'center',hidden: true},
                        {text: 'Text', datafield: 'year_weekend_day', cellsalign: 'center', align: 'center',hidden: true},
                        {text: 'Text', datafield: 'text', cellsalign: 'center', align: 'center'},
                        {
                            text: '',
                            datafield: 'Edit',
                            filterable: false,
                            width: 38,
                            cellsrenderer: function (row) {
                                row = contacts.jqxGrid('getrowdata', row);
                                return "<a class=\"item-edit\" data-id=\"" + row.id + "\"><button class=\"btn btn-info btn-xs\" style=\"margin: 6px\"><i class=\"entypo-pencil\"></i></button></a>";
                            }
                        }
                    ]
                }).on('rowselect', function (event) {
                $('#delete_contact').toggle(contacts.jqxGrid('getselectedrowindexes').length > 0);
            }).on('rowunselect', function (event) {
                $('#delete_contact').toggle(contacts.jqxGrid('getselectedrowindexes').length > 0);
            });
            $('body').on('click', '.item-edit', function () {
                var edit = $('#edit');
                
                edit.find('#sid').val($(this).data('id'));
                var sid = $(this).data('id');
                $.ajax({
                    url: "{{url('messages/send1')}}",
                    type: 'POST',
                    data:{id:sid},
                    success: function(result){
                        edit.find('#text').val(result['text']);
                        $('#mms_url').val(result['mms']);
                        if(result['repeat'] == -2) {
                            edit.find('#schedule_repeat').val('Never');
                            edit.find('.repeat_after').css('display','none');
                            edit.find('#repeat_on_date').css('display','none');
                        } else if(result['repeat'] == -1) {
                            edit.find('#schedule_repeat').val('On Date');
                            edit.find('.repeat_after').css('display','none');
                            edit.find('#repeat_on_date').css('display','unset');
                            edit.find('#repeat_on_date').val(result['repeat_end']);
                        } else {
                            edit.find('#schedule_repeat').val('After');
                            edit.find('.repeat_after').css('display','unset');
                            edit.find('.end_repeat_time').val(result['repeat']);
                            edit.find('#repeat_on_date').css('display','none');
                        }
                        let st = result['start_time'].split(':');
                        let st1 = st[0]%12;
                        if(st[0] > 12) {
                            st1 = st1 + ':' + st[1] + " PM";
                        } else {
                            st1 = st1 + ':' + st[1] + " AM";
                        }
                        edit.find('#schedule_start_at_timeP').val(st1);
                        edit.find('#schedule_start_at_time').val(st[0] + ":" + st[1]);
                        if(result['frequency'] == 'daily') {
                            $('.frequency_div').find('input[type=radio]').eq(0).prop("checked",true);
                            $('input[name=daily_period]').val(result['every']);
                            $('.daily').css('display','unset');
                            $('.weekly').css('display','none');
                            $('.monthly').css('display','none');
                            $('.yearly').css('display','none');
                        } else if(result['frequency'] == 'weekly') {
                            $('.frequency_div').find('input[type=radio]').eq(1).prop("checked",true);
                            $('input[name=weekly_period]').val(result['every']);
                            $('.daily').css('display','none');
                            $('.weekly').css('display','unset');
                            $('.monthly').css('display','none');
                            $('.yearly').css('display','none');

                            let temp = result['dow'].split(',');
                            for(i = 0 ; i<temp.length ; i++) {
                                $('.weekly_table').find('td').eq(temp[i]).css('background-color','#657894');
                                $('.weekly_table').find('td').eq(temp[i]).addClass('selected_td');
                            }
                        } else if(result['frequency'] == 'monthly') {
                            $('.frequency_div').find('input[type=radio]').eq(2).prop("checked",true);
                            $('input[name=monthly_period]').val(result['every']);
                            $('.daily').css('display','none');
                            $('.weekly').css('display','none');
                            $('.monthly').css('display','unset');
                            $('.yearly').css('display','none');
                            if(result['month_weekend_turn'] != null) {
                                edit.find('.monthly_table').removeClass('enabled_table');
                                edit.find('.monthly_table').css('opacity','0.5');
                                edit.find('.monthly input[type=radio]').eq(1).prop("checked",true);
                                edit.find('#monthly_turn').prop("disabled",false);
                                edit.find('#monthly_turn').css("opacity",'1');
                                edit.find('#monthly_turn').val(result['month_weekend_turn']);
                                edit.find('#monthly_day').prop("disabled",false);
                                edit.find('#monthly_day').css("opacity",'1');
                                edit.find('#monthly_day').val(result['month_weekend_day']);
                            } else {
                                edit.find('.monthly_table').addClass('enabled_table');
                                edit.find('.monthly_table').css('opacity','1');
                                edit.find('.monthly input[type=radio]').eq(0).prop("checked",true);
                                let temp = result['dom'].split(',');
                                for(i = 0 ; i<temp.length ; i++) {
                                    $('.monthly_table').find('td').eq(temp[i]-1).css('background-color','#657894');
                                    $('.monthly_table').find('td').eq(temp[i]-1).addClass('selected_td');
                                }
                            }
                        } else if(result['frequency'] == 'yearly') {
                            $('.frequency_div').find('input[type=radio]').eq(3).prop("checked",true);
                            $('input[name=yearly_period]').val(result['every']);
                            $('.daily').css('display','none');
                            $('.weekly').css('display','none');
                            $('.monthly').css('display','none');
                            $('.yearly').css('display','unset');

                            let temp = result['doy'].split(',');
                            for(i = 0 ; i<temp.length ; i++) {
                                $('.yearly_table').find('td').eq(temp[i]-1).css('background-color','#657894');
                                $('.yearly_table').find('td').eq(temp[i]-1).addClass('selected_td');
                            }

                            if(result['year_weekend_turn'] != null) {
                                edit.find('input[name=yearly_on]').prop("checked",true);
                                edit.find('#yearly_turn').css('opacity','1');
                                edit.find('#yearly_turn').prop('disable',false);
                                edit.find('#yearly_day').css('opacity','1');
                                edit.find('#yearly_day').prop('disable',false);
                            }
                        }
                    }
                });
                edit.modal('show', {backdrop: 'static'});
            });
            $('#edit,#create').on('hidden.bs.modal', function () {
                if ($(this).find('form').data('updated')) {
                    contacts.jqxGrid('updatebounddata');
                }
            });

            $('#create').on('shown.bs.modal', function () {
                $(this).find('.avatar').attr('src', Config.defaultURL + '/assets/images/member.jpg');
            });

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $(input).prev('.avatar').attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#import-contacts').find('input[name=contacts]').change(function () {
                $(this).closest('form').submit();
            });
            $('#create,#edit').find('input[name=avatar]').change(function () {
                readURL(this);
            });






            let sms_single = $('#edit');

            sms_single.on("click",'input[name=frequency_type]',function() {
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
            }).on('click','.btn-info',function() {
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
        });

    </script>
@endpush
