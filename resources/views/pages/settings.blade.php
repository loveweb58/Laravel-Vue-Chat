@extends('layouts.dashboard', ['current_page'=>'Settings', 'title'=>'Settings'])

@section('content')
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title">Edit Profile</div>
                <div class="panel-options">
                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                </div>
            </div>
            <div class="panel-body">
                <form name="form" id="edit-profile" action="{{url('settings/profile')}}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal form-groups-bordered validate ajax-form">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <div class="alert alert-success" style="display: none">
                    </div>
                    <div class="col-md-offset-6">
                        <div class="form-group">
                            <img src="{{url(Auth::user()->avatar)}}" class="avatar" style="height: 94px;width: 96px;" onclick="$(this).next('input[name=avatar]').click();">
                            <input type="file" name="avatar" style="display:none" accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="first_name" class="col-md-3 control-label">First Name</label>
                            <div class="col-md-3">
                                <input class="form-control" id="first_name" name="first_name" value="{{Auth::user()->first_name}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="last_name" class="col-md-3 control-label">Last Name</label>
                            <div class="col-md-3">
                                <input class="form-control" id="last_name" name="last_name" value="{{Auth::user()->last_name}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="phone" class="col-md-3 control-label">Phone</label>
                            <div class="col-md-3">
                                <input class="form-control" id="phone" name="phone" value="{{Auth::user()->phone}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="country" class="col-md-3 control-label">Country</label>
                            <div class="col-md-3">
                                <input class="form-control" id="country" name="country" value="{{Auth::user()->country}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="state" class="col-md-3 control-label">State</label>
                            <div class="col-md-3">
                                <input class="form-control" id="state" name="state" value="{{Auth::user()->state}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="city" class="col-md-3 control-label">City</label>
                            <div class="col-md-3">
                                <input class="form-control" id="city" name="city" value="{{Auth::user()->city}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="address" class="col-md-3 control-label">Address</label>
                            <div class="col-md-3">
                                <input class="form-control" id="address" name="address" value="{{Auth::user()->address}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="signature" class="col-md-3 control-label">Signature</label>
                            <div class="col-md-3">
                                <input class="form-control" id="signature" name="signature" value="{{Auth::user()->signature}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="did_sender" class="col-md-3 control-label">Sms Sender</label>
                            <div class="col-md-3">
                                <select id="did_sender" name="did_sender" class="selectboxit form-control">
                                    @foreach($did as $n)
                                        @if((Auth::user()->did_sender->id ?? -1) == $n->id)
                                            <option value="{{$n->id}}" selected>{{$n->did}}</option>
                                        @else
                                            <option value="{{$n->id}}">{{$n->did}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-offset-3">
                        <div class="form-group">
                            <label for="forward2email" class="col-md-3 control-label">Sms To Email</label>
                            <div class="col-md-3">
                                <select id="forward2email" name="forward2email" class="selectboxit form-control">
                                    @if(Auth::user()->forward2email)
                                        <option value="1" selected>Yes</option>
                                        <option value="0">No</option>
                                    @else
                                        <option value="1">Yes</option>
                                        <option value="0" selected>No</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="text-align: center">
                        <button class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title">Change Password</div>
                <div class="panel-options">
                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                </div>
            </div>
            <div class="panel-body">
                @if($errors->password->any())
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            >×</span>
                        </button>
                        {{$errors->password->first()}}
                    </div>
                @endif
                @if(Session::has('password.message'))
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            >×</span>
                        </button>
                        {{Session::get('password.message')}}
                    </div>
                @endif
                <form name="form" action="{{url('settings/change-password')}}" method="post"
                      enctype="multipart/form-data"
                      class="form-horizontal form-groups-bordered validate">

                    {{csrf_field()}}

                    <div class="col-sm3 col-sm-offset-3">
                        <div class="form-group{{ $errors->password->has('old_password') ? ' has-error' : '' }}">
                            <label for="old_password" class="col-sm-3 control-label">Current Password</label>

                            <div class="col-sm-3">
                                <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Current Password">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm3 col-sm-offset-3">
                        <div class="form-group{{ $errors->password->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-sm-3 control-label">New Password</label>
                            <div class="col-sm-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="New Password">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm3 col-sm-offset-3">
                        <div class="form-group{{ $errors->password->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password_confirmation" class="col-sm-3 control-label">Config New
                                                                                              Password</label>
                            <div class="col-sm-3">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="New Password">
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="text-align: center">
                        <button class="btn btn-success">Change Password</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title">Two Factor Auth</div>
                <div class="panel-options">
                    <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                </div>
            </div>
            <div class="panel-body">
                @if($errors->ga->any())
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            >×</span>
                        </button>
                        {{$errors->ga->first()}}
                    </div>
                @endif
                @if(Session::has('ga.message'))
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            >×</span>
                        </button>
                        {{Session::get('ga.message')}}
                    </div>
                @endif
                <div style="text-align: center">
                    <p><a href="{{url("https://support.google.com/accounts/answer/1066447?hl=en")}}" target="_blank">Download
                                                                                                                     App</a>
                    </p>
                    <img src="{{url($gaImage)}}">
                </div>
                @if(Auth::user()->ga_secret == '')
                    <form name="form" action="{{url('settings/ga')}}" method="post"
                          enctype="multipart/form-data"
                          class="form-horizontal form-groups-bordered validate">
                        {{csrf_field()}}
                        <div class="form-group" style="text-align: center">
                            <button class="btn btn-success">Generate</button>
                        </div>
                    </form>
                @else
                    <form name="form" action="{{url('settings/ga')}}" method="post"
                          enctype="multipart/form-data"
                          class="form-horizontal form-groups-bordered validate">
                        {{csrf_field()}}
                        {{method_field('DELETE')}}
                        <div class="form-group" style="text-align: center">
                            <button class="btn btn-danger">Remove</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @if(Auth::user()->can('docs.view') && Auth::user()->account->limits('api', false))
        <div class="row" style="padding-bottom: 50px">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">API Settings</div>
                    <div class="panel-options">
                        <a href="{{url('settings/api-token')}}" title="Generate Token"><i class="entypo-arrows-ccw"></i></a>
                        <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                    </div>
                </div>
                <div class="panel-body">
                    <div style="text-align: center">
                        API Token
                        <code>
                            {{Auth::user()->api_token}}
                        </code>
                    </div>
                    <form class="form-horizontal form-groups-bordered validate ajax-form" action="{{url("settings/api-sms")}}" method="POST" enctype="multipart/form-data" style="margin-top: 15px">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="col-sm3 col-sm-offset-3">
                            <div class="form-group">
                                <label for="receive_url" class="col-sm-3 control-label">SMS Receive URL</label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="receive_url" id="receive_url" value="{{Auth::user()->account->setting('api.sms.receive.url')}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="text-align: center">
                            <button class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
@push('styles')
@endpush
@push('scripts')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(input).prev('.avatar').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#edit-profile').find('input[name=avatar]').change(function () {
            readURL(this);
        });
    </script>
@endpush
