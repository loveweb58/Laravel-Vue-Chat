@extends('layouts.dashboard', ['current_page'=>'Sms Forwarding', 'title'=>'Sms Forwarding'])

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
                <div class="panel-heading">
                    <div class="panel-title">
                        @if(Auth::user()->can('forwarding.create'))
                            <a href="#create" data-toggle="modal">
                                <button class="btn btn-success btn-sm btn-icon icon-left">
                                    <i class="entypo-plus"></i>Create
                                </button>
                            </a>
                        @endif
                        @if(Auth::user()->can('forwarding.delete'))
                            <a href="#" id="delete">
                                <button class="btn btn-danger btn-sm btn-icon icon-left" style="margin-left: 10px;">
                                    <i class="entypo-trash"></i>Delete
                                </button>
                            </a>
                        @endif
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
                                <th class="column-title">Did</th>
                                <th class="column-title">Number</th>
                                <th class="column-title">Forward To</th>
                                <th class="column-title">Enable</th>
                                <th class="column-title no-link last" style="width: 40px">
                                    <span class="nobr">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="forwarding_table">
                            @foreach($forwards as $forward)
                                <tr class="even pointer">
                                    <td>
                                        <input type="checkbox" name="select" id="status_{{$forward->id}}" data-id="{{$forward->id}}" title="Check"/>
                                    </td>
                                    <td>{{$forward->did->did}}</td>
                                    <td>{{$forward->number->did}}</td>
                                    <td>{{$forward->forward_to}}</td>
                                    <td>{{$forward->enabled ? 'Yes' : 'No'}}</td>
                                    <td class="last text-center">
                                        @if(Auth::user()->can('forwarding.update'))
                                            <a class="item-edit" data-id="{{$forward->id}}">
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
                        {{ $forwards->links() }}
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
                    <h4 class="modal-title">Create Forwarding</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("forwards")}}" method="post">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="did_id" class="control-label">Did</label>
                                    <select class="form-control selectboxit" id="did_id" name="did_id">
                                        @foreach($did as $d)
                                            <option value="{{$d->id}}">{{$d->did}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tmp_did_id" class="control-label">Number</label>
                                    <select class="form-control selectboxit" id="tmp_did_id" name="tmp_did_id">
                                        @foreach($did as $d)
                                            <option value="{{$d->id}}">{{$d->did}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="forward_to" class="control-label">Forward To</label>
                                    <input name="forward_to" class="form-control" id="forward_to">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enabled" class="control-label">Status</label>
                                    <select class="form-control selectboxit" id="enabled" name="enabled">
                                        <option value="1">Enabled</option>
                                        <option value="0">Disabled</option>
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
    <div class="modal fade" id="edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Forwarding</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("forwards/id")}}" method="post">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="did_id" class="control-label">Did</label>
                                    <select class="form-control selectboxit" id="did_id" name="did_id">
                                        @foreach($did as $d)
                                            <option value="{{$d->id}}">{{$d->did}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tmp_did_id" class="control-label">Number</label>
                                    <select class="form-control selectboxit" id="tmp_did_id" name="tmp_did_id">
                                        @foreach($did as $d)
                                            <option value="{{$d->id}}">{{$d->did}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="forward_to" class="control-label">Forward To</label>
                                    <input name="forward_to" class="form-control" id="forward_to">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enabled" class="control-label">Status</label>
                                    <select class="form-control selectboxit" id="enabled" name="enabled">
                                        <option value="1">Enabled</option>
                                        <option value="0">Disabled</option>
                                    </select>
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

            var appoiments_table = $('#forwarding_table');

            $('.item-edit').on('click', function () {
                $.get(Config.defaultURL + "/forwards/" + $(this).data('id') + "/edit", function (data) {
                    var edit = $('#edit');
                    edit.find('form')
                        .data('updated', false).attr('action', Config.defaultURL + '/forwards/' + data.id)
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
            });

            $("#delete").on('click', function (e) {
                e.preventDefault();
                BootstrapDialog.confirm({
                    title: 'Forwarding Delete',
                    message: 'Do you really want to delete forwarding?',
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
                            $.post(Config.defaultURL + "/forwards", {
                                _method: 'DELETE',
                                id: del_id
                            }, function () {
                                window.location.reload(true);
                            }, "json");
                        }
                    }
                });
            });
        });
    </script>
@endpush