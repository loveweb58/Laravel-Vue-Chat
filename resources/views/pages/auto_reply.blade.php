@extends('layouts.dashboard', ['current_page'=>'Auto Reply', 'title'=>'Auto Reply'])

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
                <div class="panel-heading">
                    <div class="panel-title">
                        @if(Auth::user()->can('auto_reply.create'))
                            <a href="#create" data-toggle="modal">
                                <button class="btn btn-success btn-sm btn-icon icon-left">
                                    <i class="entypo-plus"></i>Create
                                </button>
                            </a>
                        @endif
                    </div>
                    <div class="panel-options">
                        <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                    </div>
                </div>
                <div class="panel-body">
                    @if($replies->isNotEmpty())
                        <div class="dd">
                            @include('partials.auto_reply', ['items' => $replies])
                        </div>
                    @else
                        <div style="text-align: center;color: red">Records Not Found</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Auto Reply</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("auto-reply")}}" method="post" enctype="multipart/form-data">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="source" class="control-label">Source</label>
                                    <input name="source" class="form-control" id="source" value="*">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="did_id" class="control-label">Destination</label>
                                    <select class="form-control selectboxit" id="did_id" name="did_id">
                                        @foreach($did as $d)
                                            <option value="{{$d->id}}">{{$d->did}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="keyword" class="control-label">Keyword</label>
                                    <input name="keyword" class="form-control" id="keyword">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="text" class="control-label">Reply Text</label>
                                    @if(Auth::user()->account->messageTemplates->count() > 0 && Auth::user()->account->limits('ar_message_templates', false))
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
                                    <textarea rows="1" name="text" class="form-control autogrow" id="text" style="resize: vertical"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="action" class="control-label">Action</label>
                                    <select class="form-control selectboxit" id="action" name="action">
                                        <option value="">Nothing</option>
                                        <option value="update_first_name">Update First Name</option>
                                        <option value="update_last_name">Update Last Name</option>
                                        <option value="update_name">Update Name</option>
                                        <option value="schedule">Schedule</option>
                                        <option value="appointment_register">Appointment(Register)</option>
                                        <option value="appointment_cancel">Appointment(Cancel)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->account->limits("auto_reply_weekdays", false) || Auth::user()->account->limits("auto_reply_date", false))
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs bordered">
                                        @if(Auth::user()->account->limits("auto_reply_weekdays", false))
                                            <li class="active">
                                                <a href="#weekdays" data-toggle="tab">
                                                    <span>Weekdays</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if(Auth::user()->account->limits("auto_reply_date", false))
                                            <li>
                                                <a href="#date" data-toggle="tab">
                                                    <span>Custom Date</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <div class="tab-content">
                                        @if(Auth::user()->account->limits("auto_reply_weekdays", false))
                                            <div class="tab-pane active" id="weekdays">
                                                <div class="scrollable" data-height="120">
                                                    @foreach($weekdays as $k=>$v)
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <div class="col-md-12">
                                                                        <label for="weekdays[{{$k}}][status]" class="control-label">{{$v}}</label>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="make-switch" data-on-label="<i class='entypo-check'></i>" data-off-label="<i class='entypo-cancel'></i>">
                                                                            <input type="checkbox" name="weekdays[{{$k}}][status]" id="weekdays[{{$k}}][status]" checked/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="weekdays[{{$k}}][from]" class="control-label">Time
                                                                                                                              From</label>
                                                                    <input type="time" class="form-control" id="weekdays[{{$k}}][from]" name="weekdays[{{$k}}][from]" value="00:00" min="00:00" max="23:59">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="weekdays[{{$k}}][to]" class="control-label">Time
                                                                                                                            To</label>
                                                                    <input type="time" class="form-control" id="weekdays[{{$k}}][to]" name="weekdays[{{$k}}][to]" value="23:59" min="00:00" max="23:59">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if(Auth::user()->account->limits("auto_reply_date", false))
                                            <div class="tab-pane" id="date">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="date[date]" class="control-label">Date</label>
                                                            <input class="form-control datepicker2" id="date[date]" name="date[date]" value="{{\Carbon\Carbon::now()->toDateString()}}" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="date[from]" class="control-label">Time
                                                                                                          From</label>
                                                            <input type="time" class="form-control" id="date[from]" name="date[from]" value="00:00" min="00:00" max="23:59" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="date[to]" class="control-label">Time To</label>
                                                            <input type="time" class="form-control" id="date[to]" name="date[to]" value="23:59" min="00:00" max="23:59" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(Auth::user()->account->limits('mms', false))
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="info" class="control-label">Info</label>
                                    <input name="info" class="form-control" id="info">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button class="btn btn-success">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Auto Reply</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("auto-reply/id")}}" method="post">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="source" class="control-label">Source</label>
                                    <input name="source" class="form-control" id="source" value="*">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="did_id" class="control-label">Destination</label>
                                    <select class="form-control selectboxit" id="did_id" name="did_id">
                                        @foreach($did as $d)
                                            <option value="{{$d->id}}">{{$d->did}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="keyword" class="control-label">Keyword</label>
                                    <input name="keyword" class="form-control" id="keyword">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="text" class="control-label">Reply Text</label>
                                    @if(Auth::user()->account->messageTemplates->count() > 0 && Auth::user()->account->limits('ar_message_templates', false))
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
                                    <textarea rows="1" name="text" class="form-control autogrow" id="text" style="resize: vertical"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="action" class="control-label">Action</label>
                                    <select class="form-control selectboxit" id="action" name="action">
                                        <option value="">Nothing</option>
                                        <option value="update_first_name">Update First Name</option>
                                        <option value="update_last_name">Update Last Name</option>
                                        <option value="update_name">Update Name</option>
                                        <option value="schedule">Schedule</option>
                                        <option value="appointment_register">Appointment(Register)</option>
                                        <option value="appointment_cancel">Appointment(Cancel)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->account->limits("auto_reply_weekdays", false) || Auth::user()->account->limits("auto_reply_date", false))
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs bordered">
                                        @if(Auth::user()->account->limits("auto_reply_weekdays", false))
                                            <li class="active">
                                                <a href="#weekdays-edit" data-toggle="tab">
                                                    <span>Weekdays</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if(Auth::user()->account->limits("auto_reply_date", false))
                                            <li>
                                                <a href="#date-edit" data-toggle="tab">
                                                    <span>Custom Date</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <div class="tab-content">
                                        @if(Auth::user()->account->limits("auto_reply_weekdays", false))
                                            <div class="tab-pane active" id="weekdays-edit">
                                                <div class="scrollable" data-height="120">
                                                    @foreach($weekdays as $k=>$v)
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <div class="col-md-12">
                                                                        <label for="weekdays[{{$k}}][status]" class="control-label">{{$v}}</label>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="make-switch" data-on-label="<i class='entypo-check'></i>" data-off-label="<i class='entypo-cancel'></i>">
                                                                            <input type="checkbox" name="weekdays[{{$k}}][status]" id="weekdays[{{$k}}][status]" checked/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="weekdays[{{$k}}][from]" class="control-label">Time
                                                                                                                              From</label>
                                                                    <input type="time" class="form-control" id="weekdays[{{$k}}][from]" name="weekdays[{{$k}}][from]" value="00:00" min="00:00" max="23:59">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="weekdays[{{$k}}][to]" class="control-label">Time
                                                                                                                            To</label>
                                                                    <input type="time" class="form-control" id="weekdays[{{$k}}][to]" name="weekdays[{{$k}}][to]" value="23:59" min="00:00" max="23:59">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if(Auth::user()->account->limits("auto_reply_date", false))
                                            <div class="tab-pane" id="date-edit">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="date[date]" class="control-label">Date</label>
                                                            <input class="form-control datepicker2" id="date[date]" name="date[date]" value="{{\Carbon\Carbon::now()->toDateString()}}" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="date[from]" class="control-label">Time
                                                                                                          From</label>
                                                            <input type="time" class="form-control" id="date[from]" name="date[from]" value="00:00" min="00:00" max="23:59" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="date[to]" class="control-label">Time To</label>
                                                            <input type="time" class="form-control" id="date[to]" name="date[to]" value="23:59" min="00:00" max="23:59" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(Auth::user()->account->limits('mms', false))
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
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enabled" class="control-label">Status</label>
                                    <select class="form-control selectboxit" id="enabled" name="enabled">
                                        <option value="1">Enabled</option>
                                        <option value="0">Disabled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="info" class="control-label">Info</label>
                                    <input name="info" class="form-control" id="info">
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
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/minimal/_all.css') }}">
    <link href="{{asset('assets/plugins/jquery-nestable/css/style.css')}}" rel="stylesheet">
    <style>
        .page-body .select2-container .select2-choice {
            height: 31px;
            line-height: 31px;
            margin: 0
        }

        .select2-drop {
            z-index: 100000 !important;
        }

        .dd-handle {
            background-color: rgb(250, 250, 250);
        }

        .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 240);
        }

        .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 230);
        }

        .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 220);
        }

        .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 210);
        }

        .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 200);
        }

        .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 190);
        }

        .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 180);
        }

        .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 170);
        }

        .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 160);
        }

        .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle + .dd-list .dd-handle {
            background-color: rgb(250, 250, 150);
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{asset('assets/plugins/jquery-nestable/js/jquery.nestable.js')}}"></script>
    <script>
        $(function () {

            $("#create").find('.nav-tabs a:first').tab('show');

            $('input[name^="weekdays"][type=checkbox]').on('change', function () {
                if (this.checked) {
                    $(this).closest(".row").find("input[type=time]").removeAttr("readonly");
                } else {
                    $(this).closest(".row").find("input[type=time]").attr("readonly", true);
                }
            });

            $('a[data-toggle="tab"]').on("show.bs.tab", function (e) {
                $($(e.relatedTarget).attr('href')).find('input').attr('disabled', true);
                $($(e.target).attr('href')).find('input').removeAttr('disabled');
            });

            var appoiments_table = $('#auto_reply_table');

            $('.item-edit').on('click', function () {
                $.get(Config.defaultURL + "/auto-reply/" + $(this).data('id') + "/edit", function (data) {
                    var edit = $('#edit');
                    edit.find('form')
                        .data('updated', false).attr('action', Config.defaultURL + '/auto-reply/' + data.id)
                        .end()
                        .find('.alert')
                        .hide();
                    jQuery.each(data, function (k, v) {
                        edit.find('[name=' + k + '][type!=file]').val(v).trigger("change");
                        if (k === "weekdays" && v !== null) {
                            edit.find('.nav-tabs a[href="#weekdays-edit"]').tab('show');
                            $.each(v, function (k, v) {
                                if (v.status) {
                                    edit.find("[name=weekdays\\[" + k + "\\]\\[status\\]]")
                                        .attr("checked", true)
                                        .trigger("change");
                                } else {
                                    edit.find("[name=weekdays\\[" + k + "\\]\\[status\\]]")
                                        .removeAttr("checked")
                                        .trigger("change");
                                }
                                edit.find("[name=weekdays\\[" + k + "\\]\\[from\\]]").val(v.from);
                                edit.find("[name=weekdays\\[" + k + "\\]\\[to\\]]").val(v.to);
                            });
                        }
                        if (k === "date" && v !== null) {
                            edit.find('.nav-tabs a[href="#date-edit"]').tab('show');
                            edit.find("[name=date\\[date\\]]").val(v.date);
                            edit.find("[name=date\\[from\\]]").val(v.from);
                            edit.find("[name=date\\[to\\]]").val(v.to);
                        }
                        if (jQuery.isArray(v)) {
                            edit.find('select[name=' + k + '\\[\\]]').val(v).trigger("change");
                        }
                    });
                    $.each(edit.find(".autogrow"), function (k, v) {
                        $(v).trigger('input');
                    });
                    edit.modal('show', {backdrop: 'static'});
                }, "json");
            });

            $('#check_all').on('change', function () {
                appoiments_table.find("input[name=select][type='checkbox']").prop('checked', this.checked);
            });

            $('#date_filter').on('change', function () {
                $(this).closest('form').submit();
            });

            appoiments_table.on('change', function () {
                $('#check_all')
                    .prop('checked', appoiments_table.find("input[name=select][type='checkbox']:checked").length === appoiments_table.find("input[name=select][type='checkbox']").length);
            });
            $('#edit,#create').on('hidden.bs.modal', function () {
                if ($(this).find('form').data('updated')) {
                    window.location.reload(true);
                }
            }).find("[data-action=\"insert_template\"]").find('a').on('click', function () {
                $(this).closest('form').find('[name="text"]').val($(this).data('text'));
            });

            $("#delete").on('click', function (e) {
                e.preventDefault();
                BootstrapDialog.confirm({
                    title: 'Auto Reply Delete',
                    message: 'Do you really want to delete auto reply?',
                    type: BootstrapDialog.TYPE_DANGER,
                    closable: true,
                    draggable: false,
                    btnCancelLabel: 'Close',
                    btnOKLabel: 'Delete',
                    btnOKClass: 'btn-danger',
                    callback: function (result) {
                        if (result) {
                            del_id = [];
                            appoiments_table.find("input[name=select][type='checkbox']:checked").each(function (k, v) {
                                del_id.push($(v).data('id'));
                            });
                            $.post(Config.defaultURL + "/auto-reply", {
                                _method: 'DELETE',
                                id: del_id
                            }, function () {
                                window.location.reload(true);
                            }, "json");
                        }
                    }
                });
            });

            $('.dd').nestable().on('change', function () {
                $.post(Config.defaultURL + "/auto-reply/sort", {
                    items: $(this)
                        .nestable('serialize')
                }, function (data) {

                }, "json");
            });

            $('.datepicker2').datepicker({format: 'yyyy-mm-dd', autoclose: true});
        });
    </script>
@endpush