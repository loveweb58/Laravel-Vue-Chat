@extends('layouts.dashboard', ['current_page'=>'Appointments', 'title'=>'Appointments'])

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
                <div class="panel-heading">
                    <div class="panel-title">
                        @if(Auth::user()->can('appointments.create'))
                            <a href="#create" data-toggle="modal">
                                <button class="btn btn-success btn-sm btn-icon icon-left">
                                    <i class="entypo-plus"></i>Create
                                </button>
                            </a>
                        @endif
                        @if(Auth::user()->can('appointments.delete'))
                            <a href="#" id="delete">
                                <button class="btn btn-danger btn-sm btn-icon icon-left" style="margin-left: 10px;">
                                    <i class="entypo-trash"></i>Delete
                                </button>
                            </a>
                        @endif
                        @if(Auth::user()->can('appointments.update'))
                            <a href="#settings" data-toggle="modal">
                                <button class="btn btn-default btn-sm btn-icon icon-left" style="margin-left: 10px;">
                                    <i class="entypo-cog"></i>Settings
                                </button>
                            </a>
                        @endif
                        <form action="{{url()->current()}}" method="GET" style="display: inline-block;width: auto;margin-left: 20px">
                            <b>Date</b>
                            <input class="form-control datepicker2" id="date_filter" name="date" value="{{$today}}" title="Date Filter" style="display: inline-block;width: auto;margin-left: 20px">
                        </form>
                    </div>
                    <div class="panel-options">
                        <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-hover table-striped table-responsive">
                            <thead>
                            <tr class="headings">
                                <th class="column-title" style="width: 24px">
                                    <input type="checkbox" id="check_all" title="Check"/>
                                </th>
                                <th class="column-title">Time</th>
                                <th class="column-title">Status</th>
                                <th class="column-title">Number</th>
                                <th class="column-title">Subject</th>
                                <th class="column-title">Created At</th>
                                <th class="column-title">Updated At</th>
                                <th class="column-title no-link last" style="width: 40px">
                                    <span class="nobr">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="appointments_table">
                            @foreach($appointments as $appointment)
                                <tr class="even pointer">
                                    <td>
                                        <input type="checkbox" name="select" id="status_{{$appointment->id}}" data-id="{{$appointment->id}}" title="Check"/>
                                    </td>
                                    <td>{{$appointment->date}}</td>
                                    <td>{{$appointment->status}}</td>
                                    <td>{{Auth::user()->account->numberToContact($appointment->number)}}</td>
                                    <td>{{$appointment->subject}}</td>
                                    <td>{{$appointment->created_at}}</td>
                                    <td>{{$appointment->updated_at}}</td>
                                    <td class="last text-center">
                                        @if(Auth::user()->can('appointments.update'))
                                            <a class="item-edit" data-id="{{$appointment->id}}">
                                                <button class="btn btn-info btn-xs" title="Edit">
                                                    <i class="entypo-pencil"></i>
                                                </button>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Appointment</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("appointments")}}" method="post">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date" class="control-label">Date</label>
                                    <input class="form-control daterangepicker2" value="{{$today}}" id="date" name="date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_hour" class="control-label">Hours From</label>
                                    <select class="form-control selectboxit" id="min_hour" name="min_hour">
                                        @foreach($hours as $hour)
                                            <option value="{{$hour}}">{{$hour}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_hour" class="control-label">Hours To</label>
                                    <select class="form-control selectboxit" id="max_hour" name="max_hour">
                                        @foreach($hours as $hour)
                                            @if($hour != "00:00")
                                                <option value="{{$hour}}">{{$hour}}</option>
                                            @endif
                                        @endforeach
                                        <option value="23:59">24:00</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="visits" class="control-label">Visits Per Hour</label>
                                    <select class="form-control selectboxit" id="visits" name="visits">
                                        @for($i=1;$i<7;$i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
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
    <div class="modal fade" id="settings">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Appointment Settings</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("appointments/settings")}}" method="post">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="success" class="control-label">Success Text</label>
                                    <textarea name="success" class="form-control" id="success" rows="3" style="resize: vertical">{{Auth::user()->account->setting('appointments.texts.success')}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="not_available" class="control-label">Not Available Text</label>
                                    <textarea name="not_available" class="form-control" id="not_available" rows="3" style="resize: vertical">{{Auth::user()->account->setting('appointments.texts.not_available')}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="cancel" class="control-label">Cancel Text</label>
                                    <textarea name="cancel" class="form-control" id="cancel" rows="3" style="resize: vertical">{{Auth::user()->account->setting('appointments.texts.cancel')}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="cancel_error" class="control-label">Cancel Error Text</label>
                                    <textarea name="cancel_error" class="form-control" id="cancel_error" rows="3" style="resize: vertical">{{Auth::user()->account->setting('appointments.texts.cancel_error')}}</textarea>
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
    <div class="modal fade" id="edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Appointment</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("appointments/id")}}" method="post">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject" class="control-label">Subject</label>
                                    <input name="subject" class="form-control" id="subject">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="control-label">Status</label>
                                    <select class="form-control selectboxit" id="status" name="status">
                                        <option value="available">Available</option>
                                        <option value="canceled">Canceled</option>
                                        <option value="closed">Closed</option>
                                        <option value="busy">Busy</option>
                                    </select>
                                </div>
                            </div>
                        </div>
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
                            <button class="btn btn-info">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .page-body .select2-container .select2-choice {
            height: 31px;
            line-height: 31px;
            margin: 0
        }

        .select2-drop {
            z-index: 100000 !important;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(function () {

            var appoiments_table = $('#appointments_table');

            $('.item-edit').on('click', function () {
                $.get(Config.defaultURL + "/appointments/" + $(this).data('id') + "/edit", function (data) {
                    var edit = $('#edit');
                    edit.find('form')
                        .data('updated', false).attr('action', Config.defaultURL + '/appointments/' + data.id)
                        .end()
                        .find('.alert')
                        .hide();
                    jQuery.each(data, function (k, v) {
                        edit.find('[name=' + k + ']').val(v).trigger("change");
                        if (jQuery.isArray(v)) {
                            edit.find('select[name=' + k + '\\[\\]]').val(v).trigger("change");
                        }
                    });
                    edit.modal('show', {backdrop: 'static'});
                }, "json");
            });

            $('#check_all').on('change', function () {
                appoiments_table.find("input[name=select][type='checkbox']").prop('checked', this.checked);
            });

            appoiments_table.on('change', function () {
                $('#check_all')
                    .prop('checked', appoiments_table.find("input[name=select][type='checkbox']:checked").length === appoiments_table.find("input[name=select][type='checkbox']").length);
            });
            $('#edit,#create,#settings').on('hidden.bs.modal', function () {
                if ($(this).find('form').data('updated')) {
                    window.location.reload(true);
                }
            });

            $("#delete").on('click', function (e) {
                e.preventDefault();
                BootstrapDialog.confirm({
                    title: 'Appointment Delete',
                    message: 'Do you really want to delete appointment?',
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
                            $.post(Config.defaultURL + "/appointments", {
                                _method: 'DELETE',
                                id: del_id
                            }, function () {
                                window.location.reload(true);
                            }, "json");
                        }
                    }
                });
            });

            $('.datepicker2').datepicker({format: 'yyyy-mm-dd', autoclose: true});
            $('.daterangepicker2').datepicker({format: 'yyyy-mm-dd', multidate: true});

            $('#date_filter').on('changeDate', function () {
                $(this).closest('form').submit();
            });

        });


    </script>
@endpush