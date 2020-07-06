@extends('layouts.app', ['body_class' => ' login-page login-form-fall', 'title'=>'Password Reset'])

@section('body')
    <div class="login-container">
        <div class="login-header login-caret">
            <div class="login-content">
                <a href="{{url('/')}}" class="logo">
                    <img src="{{ asset('/assets/images/logo.svg') }}" width="120" alt=""/>
                </a>
            </div>
        </div>
        <div class="login-progressbar">
            <div></div>
        </div>
        <div class="login-form">
            <div class="login-content">
                @if(count($errors) > 0)
                    <div class="form-login-error" style="display: block">
                        @foreach($errors->all() as $error)
                            <h3>{{$error}}</h3>
                        @endforeach
                    </div>
                @endif
                @if (session('status'))
                    <div class="form-forgotpassword-success" style="display: block">
                        <h3>Reset email has been sent.</h3>
                        <p>Please check your email, reset password link will expire in 24 hours.</p>
                    </div>
                @endif
                <form method="post" action="{{url("password/email")}}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="entypo-user"></i>
                            </div>
                            <input class="form-control" name="email" id="email" value="{{old("email")}}" placeholder="Email" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-block btn-login">
                            Send Reset Link
                        </button>
                    </div>
                    <div class="login-bottom-links">
                        <a href="{{url('login')}}">Return to Login Page</a> <br>
                        &copy; 2017 All rights reserved by <a href="{{url("/")}}"><strong>Text My Main
                                                                                          Number</strong></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{asset('assets/js/neon-login.js')}}"></script>
@endpush