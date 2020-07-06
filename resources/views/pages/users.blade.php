@extends('layouts.dashboard', ['current_page'=>'Users', 'title'=>'Users'])

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
                <div class="panel-heading">
                    <div class="panel-title">
                        @if(Auth::user()->can('users.create'))
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
                                <th class="column-title">Username</th>
                                <th class="column-title">Email</th>
                                <th class="column-title">Name</th>
                                <th class="column-title">Created At</th>
                                <th class="column-title">Roles</th>
                                <th class="column-title no-link last" style="width: 80px">
                                    <span class="nobr">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr class="even pointer">
                                    <td class=" ">{{$loop->iteration}}</td>
                                    <td class=" ">{{$user->username}}</td>
                                    <td class=" ">{{$user->email}}</td>
                                    <td class=" ">{{$user->first_name . ' ' . $user->last_name}}</td>
                                    <td class=" ">{{$user->created_at}}</td>
                                    <td class=" ">
                                        @foreach($user->roles as $role)
                                            {{$role->display_name}}
                                            @if (!$loop->last)
                                                |
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="last text-center">
                                        @if(Auth::user()->can('users.update'))
                                            <a class="item-edit" data-id="{{$user->id}}">
                                                <button class="btn btn-info btn-xs" title="Edit">
                                                    <i class="entypo-pencil"></i>
                                                </button>
                                            </a>
                                        @endif
                                        @if($loop->iteration !== 1)
                                            @if(Auth::user()->can('users.delete'))
                                                <form style="display: inline-block" data-action="confirm" method="post" action="{{url('users/'.$user->id)}}">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <button class="btn btn-danger btn-xs" title="Delete">
                                                        <i class="entypo-cancel"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        {{ $users->links() }}
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
                    <h4 class="modal-title">Create User</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("users")}}" method="post" enctype="multipart/form-data" data-callback="clearFields">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <img src="" class="avatar" style="height: 126px;width: 128px;" onclick="$(this).next('input[name=avatar]').click();">
                                    <input type="file" name="avatar" style="display:none" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name" class="control-label">First Name</label>
                                    <input name="first_name" class="form-control" id="first_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name" class="control-label">Last Name</label>
                                    <input name="last_name" class="form-control" id="last_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="username" class="control-label">Username</label>
                                    <input name="username" class="form-control" id="username">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email" class="control-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password" class="control-label">Password</label>
                                    <input type="password" id="password" name="password" class="form-control" minlength="6">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone" class="control-label">Phone</label>
                                    <input id="phone" name="phone" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country" class="control-label">Country</label>
                                    <input name="country" class="form-control" id="country">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state" class="control-label">State</label>
                                    <input name="state" class="form-control" id="state">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city" class="control-label">City</label>
                                    <input name="city" class="form-control" id="city">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="address" class="control-label">Address</label>
                                    <input name="address" class="form-control" id="address">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="roles" class="control-label">Roles</label>
                                    <select id="roles" name="roles[]" class="select2 form-control" data-close-select2="true" multiple>
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->display_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="did" class="control-label">Did</label>
                                    <select id="did" name="did[]" class="select2 form-control" data-close-select2="true" multiple>
                                        @foreach($did as $n)
                                            <option value="{{$n->id}}">{{$n->did}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="did_sender" class="control-label">Sender</label>
                                    <select id="did_sender" name="did_sender" class="selectboxit form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="forward2email" class="control-label">Sms2Email</label>
                                    <select id="forward2email" name="forward2email" class="selectboxit form-control">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="signature" class="control-label">Signature</label>
                                    <textarea rows="1" name="signature" class="form-control autogrow" id="signature" style="resize: vertical"></textarea>
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
                    <h4 class="modal-title">Edit User</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("users/id")}}" method="post" enctype="multipart/form-data">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <img src="" class="avatar" style="height: 126px;width: 128px;" onclick="$(this).next('input[name=avatar]').click();">
                                    <input type="file" name="avatar" style="display:none" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name" class="control-label">First Name</label>
                                    <input name="first_name" class="form-control" id="first_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name" class="control-label">Last Name</label>
                                    <input name="last_name" class="form-control" id="last_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="username" class="control-label">Username</label>
                                    <input name="username" class="form-control" id="username">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email" class="control-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password" class="control-label">Password</label>
                                    <input type="password" id="password" name="password" class="form-control" minlength="6">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone" class="control-label">Phone</label>
                                    <input id="phone" name="phone" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country" class="control-label">Country</label>
                                    <input name="country" class="form-control" id="country">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state" class="control-label">State</label>
                                    <input name="state" class="form-control" id="state">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city" class="control-label">City</label>
                                    <input name="city" class="form-control" id="city">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="address" class="control-label">Address</label>
                                    <input name="address" class="form-control" id="address">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="roles" class="control-label">Roles</label>
                                    <select id="roles" name="roles[]" class="select2 form-control" data-close-select2="true" multiple>
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->display_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="did" class="control-label">Did</label>
                                    <select id="did" name="did[]" class="select2 form-control" data-close-select2="true" multiple>
                                        @foreach($did as $n)
                                            <option value="{{$n->id}}">{{$n->did}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="did_sender" class="control-label">Sender</label>
                                    <select id="did_sender" name="did_sender" class="selectboxit form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="forward2email" class="control-label">Sms2Email</label>
                                    <select id="forward2email" name="forward2email" class="selectboxit form-control">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="signature" class="control-label">Signature</label>
                                    <textarea rows="1" name="signature" class="form-control autogrow" id="signature" style="resize: vertical"></textarea>
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

        .datepicker.datepicker-dropdown {
            z-index: 100000 !important;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(function () {
            $('.item-edit').on('click', function () {
                $.get(Config.defaultURL + "/users/" + $(this).data('id') + "/edit", function (data) {
                    var edit = $('#edit');
                    edit.find('form')
                        .data('updated', false).attr('action', Config.defaultURL + '/users/' + data.id)
                        .end()
                        .find('.alert')
                        .hide();
                    jQuery.each(data, function (k, v) {
                        edit.find('[name=' + k + '][type!=password][type!=file]').val(v).trigger("change");
                        if (jQuery.isArray(v)) {
                            edit.find('select[name=' + k + '\\[\\]]').val(v).trigger("change");
                        }
                        if (data.avatar === null || !data.avatar.trim()) {
                            edit.find('.avatar').attr('src', Config.defaultURL + '/assets/images/member.jpg');
                        } else {
                            edit.find('.avatar').attr('src', data.avatar);
                        }
                    });
                    $.each(edit.find(".autogrow"), function (k, v) {
                        $(v).trigger('input');
                    });
                    edit.modal('show', {backdrop: 'static'});
                }, "json");
            });
            $('#edit,#create').on('hidden.bs.modal', function () {
                if ($(this).find('form').data('updated')) {
                    window.location.reload(true);
                }
            }).find('select[name=did\\[\\]]').on('change', function () {
                $(this)
                    .closest('form')
                    .find('select[name=did_sender]')
                    .data("selectBox-selectBoxIt")
                    .remove();
                $(this).find('option:selected').each(function () {
                    $(this)
                        .closest('form')
                        .find('select[name=did_sender]')
                        .data("selectBox-selectBoxIt")
                        .add({
                            value: $(this).val(), text: $(this).text()
                        });
                });
            });

            $('#create').on('shown.bs.modal', function () {
                $(this).find('.avatar').attr('src', Config.defaultURL + '/assets/images/member.jpg');
            });
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

        $('#create,#edit').find('input[name=avatar]').change(function () {
            readURL(this);
        });
    </script>
@endpush