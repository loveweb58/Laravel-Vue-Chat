@extends('layouts.dashboard', ['current_page'=>'Roles', 'title'=>'Roles'])

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
                <div class="panel-heading">
                    <div class="panel-title">
                        @if(Auth::user()->can('roles.create'))
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
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-hover table-striped table-responsive">
                            <thead>
                            <tr class="headings">
                                <th class="column-title">#</th>
                                <th class="column-title">System Name</th>
                                <th class="column-title">Display Name</th>
                                <th class="column-title">Created At</th>
                                <th class="column-title no-link last" style="width: 80px">
                                    <span class="nobr">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($roles as $role)
                                <tr class="even pointer">
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$role->name}}</td>
                                    <td>{{$role->display_name}}</td>
                                    <td>{{$role->created_at}}</td>
                                    <td class="last text-center">
                                        @if(Auth::user()->can('roles.update'))
                                            <a class="item-edit" data-id="{{$role->id}}">
                                                <button class="btn btn-info btn-xs" title="Edit">
                                                    <i class="entypo-pencil"></i>
                                                </button>
                                            </a>
                                        @endif
                                        @if(Auth::user()->can('roles.delete'))
                                            <form style="display: inline-block" method="post" data-action="confirm" action="{{url('roles/'.$role->id)}}">
                                                {{csrf_field()}}
                                                {{method_field('DELETE')}}
                                                <button class="btn btn-danger btn-xs" title="Delete">
                                                    <i class="entypo-cancel"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        {{ $roles->links() }}
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
                    <h4 class="modal-title">Create Role</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("roles")}}" method="post" data-callback="clearFields">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">System Name</label>
                                    <input name="name" class="form-control" id="name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="display_name" class="control-label">Display Name</label>
                                    <input name="display_name" class="form-control" id="display_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description" class="control-label">Description</label>
                                    <input name="description" class="form-control" id="description">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="permissions" class="control-label">Permissions</label>
                                    <select id="permissions" name="permissions[]" class="select2 form-control" data-close-select2="true" multiple>
                                        @foreach($permissions as $permission)
                                            <option value="{{$permission->id}}">{{$permission->display_name}}</option>
                                        @endforeach
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
                    <h4 class="modal-title">Edit Role</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("roles/id")}}" method="post">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">System Name</label>
                                    <input name="name" class="form-control" id="name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="display_name" class="control-label">Display Name</label>
                                    <input name="display_name" class="form-control" id="display_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description" class="control-label">Description</label>
                                    <input name="description" class="form-control" id="description">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="permissions" class="control-label">Permissions</label>
                                    <select id="permissions" name="permissions[]" class="select2 form-control" data-close-select2="true" multiple>
                                        @foreach($permissions as $permission)
                                            <option value="{{$permission->id}}">{{$permission->display_name}}</option>
                                        @endforeach
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
            $('.item-edit').on('click', function () {
                $.get(Config.defaultURL + "/roles/" + $(this).data('id') + "/edit", function (data) {
                    var edit = $('#edit');
                    edit.find('form')
                        .data('updated', false).attr('action', Config.defaultURL + '/roles/' + data.id)
                        .end()
                        .find('.alert')
                        .hide();
                    jQuery.each(data, function (k, v) {
                        edit.find('[name=' + k + ']').val(v);
                        if (jQuery.isArray(v)) {
                            edit.find('select[name=' + k + '\\[\\]]').val(v).trigger("change");
                        }
                    });
                    edit.modal('show', {backdrop: 'static'});
                }, "json");
            });
            $('#edit,#create').on('hidden.bs.modal', function () {
                if ($(this).find('form').data('updated')) {
                    window.location.reload(true);
                }
            });
        });


    </script>
@endpush