@extends('layouts.dashboard', ['current_page'=>'Accounts', 'title'=>'Accounts'])

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
                <div class="panel-heading">
                    <div class="panel-title">
                        @if(Auth::user()->can('accounts.create'))
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
                                <th class="column-title">Name</th>
                                <th class="column-title">Package</th>
                                <th class="column-title">Monthly Fee</th>
                                <th class="column-title">Expired At</th>
                                <th class="column-title">Created At</th>
                                <th class="column-title">Did</th>
                                <th class="column-title no-link last" style="width: 120px">
                                    <span class="nobr">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($accounts as $account)
                                <tr class="even pointer">
                                    <td class=" ">{{$loop->iteration}}</td>
                                    <td class=" ">{{$account->name}}</td>
                                    <td class=" ">{{$account->package->name}}</td>
                                    <td class=" ">{{$account->monthly_fee}}</td>
                                    <td class=" ">{{$account->expired_at}}</td>
                                    <td class=" ">{{$account->created_at}}</td>
                                    <td class=" ">
                                        @foreach($account->did as $did)
                                            {{$did->did}}
                                            @if (!$loop->last)
                                                |
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="last text-center">
                                        @if(Auth::user()->can('accounts.update'))
                                            @if($account->id !== Auth::user()->account_id)
                                                <a href="{{url("accounts/{$account->id}/auth")}}">
                                                    <button class="btn btn-primary btn-xs" title="Auth">
                                                        <i class="entypo-lock"></i>
                                                    </button>
                                                </a>
                                            @endif
                                            <a class="item-edit" data-id="{{$account->id}}">
                                                <button class="btn btn-info btn-xs" title="Edit">
                                                    <i class="entypo-pencil"></i>
                                                </button>
                                            </a>
                                        @endif
                                        @if(Auth::user()->can('accounts.delete'))
                                            <form style="display: inline-block" data-action="confirm" method="post" action="{{url('accounts/'.$account->id)}}">
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
                        {{ $accounts->links() }}
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
                    <h4 class="modal-title">Create Account</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("accounts")}}" method="post" data-callback="clearFields">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs bordered">
                                    <li class="active">
                                        <a href="#info" data-toggle="tab">
                                            <span>Info</span>
                                        </a>
                                    </li>
                                    @if(Auth::user()->account->limits("custom_labels") > 0)
                                        <li>
                                            <a href="#limits" data-toggle="tab">
                                                <span>Extra Limits</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="info">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="name" class="control-label">Name</label>
                                                    <input name="name" class="form-control" id="name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="package_id" class="control-label">Package</label>
                                                    <select id="package_id" name="package_id" class="form-control selectboxit">
                                                        @foreach($packages as $package)
                                                            <option value="{{$package->id}}">{{$package->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="extra_monthly_fee" class="control-label">Extra Monthly
                                                                                                         Fee</label>
                                                    <input name="extra_monthly_fee" class="form-control" id="extra_monthly_fee">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="expired_at" class="control-label">Expired At</label>
                                                    <input class="form-control datepicker" id="expired_at" name="expired_at" data-start-view="2" data-format="yyyy-mm-dd">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="did" class="control-label">Did</label>
                                                    <input id="did" name="did" class="form-control" data-close-select2="true"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
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
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="password" class="control-label">Password</label>
                                                    <input type="password" id="password" name="password" class="form-control" minlength="6">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
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
                                                    <label for="phone" class="control-label">Phone</label>
                                                    <input id="phone" name="phone" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="country" class="control-label">Country</label>
                                                    <input name="country" class="form-control" id="country">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="state" class="control-label">State</label>
                                                    <input name="state" class="form-control" id="state">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="city" class="control-label">City</label>
                                                    <input name="city" class="form-control" id="city">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="address" class="control-label">Address</label>
                                                    <input name="address" class="form-control" id="address">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(Auth::user()->account->limits("custom_labels") > 0)
                                        <div class="tab-pane" id="limits">
                                            <div class="row">
                                                @foreach($extra_limits as $limit)
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="limits[{{$limit['name']}}]" class="control-label">{{$limit['name']}}</label>
                                                            @if($limit['type' ] == "boolean")
                                                                <select id="limits[{{$limit['name']}}]" name="limits[{{$limit['name']}}]" class="form-control selectboxit">
                                                                    <option value="false">No</option>
                                                                    <option value="true">Yes</option>
                                                                </select>
                                                            @else
                                                                <input id="limits[{{$limit['name']}}]" name="limits[{{$limit['name']}}]" value="0" class="form-control">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
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
                    <h4 class="modal-title">Edit Account</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("accounts/id")}}" method="post">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">Name</label>
                                    <input name="name" class="form-control" id="name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="package_id" class="control-label">Package</label>
                                    <select id="package_id" name="package_id" class="form-control selectboxit">
                                        @foreach($packages as $package)
                                            <option value="{{$package->id}}">{{$package->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="extra_monthly_fee" class="control-label">Extra Monthly
                                                                                         Fee</label>
                                    <input name="extra_monthly_fee" class="form-control" id="extra_monthly_fee">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="expired_at" class="control-label">Expired At</label>
                                    <input class="form-control datepicker" id="expired_at" name="expired_at" data-start-view="2" data-format="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="did" class="control-label">Did</label>
                                    <input type="text" id="did" name="did" class="form-control" data-close-select2="true"/>
                                </div>
                            </div>
                        </div>
                        @foreach($extra_limits as $key => $limit)
                            @if($key%3==0)
                            <div class="row">
                            @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="limits[{{$limit['name']}}]" class="control-label">{{$limit['name']}}</label>
                                        @if($limit['type' ] == "boolean")
                                            <select id="limits[{{$limit['name']}}]" name="limits[{{$limit['name']}}]" class="form-control selectboxit">
                                                <option value="false">No</option>
                                                <option value="true">Yes</option>
                                            </select>
                                        @else
                                            <input id="limits[{{$limit['name']}}]" name="limits[{{$limit['name']}}]" value="0" class="form-control">
                                        @endif
                                    </div>
                                </div>
                            @if($key%3==2)
                            </div>
                            @endif
                        @endforeach
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
                $.get(Config.defaultURL + "/accounts/" + $(this).data('id') + "/edit", function (data) {
                    var edit = $('#edit');
                    edit.find('form')
                        .data('updated', false).attr('action', Config.defaultURL + '/accounts/' + data.id)
                        .end()
                        .find('.alert')
                        .hide();
                    edit.find('input[name^="limits"]').val("0");
                    jQuery.each(data, function (k, v) {
                        edit.find('input[name=' + k + '][type!=password][type!=file]').val(v).trigger("change");
                        edit.find('select[name=' + k + '][type!=password][type!=file]').val(v).trigger("change");
                        if (k === "limits" && v !== null) {
                            edit.find('select[name^="limits"]').val("false");
                            $.each(v, function (k, v) {
                                edit.find("input[name=limits\\[" + k + "\\]]").val(v).trigger('change');
                                edit.find("select[name=limits\\[" + k + "\\]]").val(v).trigger('change');
                            });
                            return true;
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
        }).find('input[name=did]').select2({
            tags: [],
            tokenSeparators: [",", " "],
            formatNoMatches: function () {
                return "Please input correct number";
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
    </script>
@endpush