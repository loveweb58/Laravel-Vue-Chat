@extends('layouts.app', ['body_class' => ' login-page login-form-fall', 'title'=>'Login'])

@section('body')
    <div class="login-container">
        <div class="login-header login-caret">
            <div class="login-content">
                <a href="{{url('/')}}" class="logo">
                    <img src="{{ asset('/assets/images/logo.svg') }}" width="120" alt=""/>
                </a>
                <p class="description">Dear user, log in to access the admin area!</p>
                <!-- progress bar indicator -->
                <div class="login-progressbar-indicator">
                    <h3>43%</h3>
                    <span>Please wait...</span>
                </div>
            </div>
        </div>
        <div class="login-progressbar">
            <div></div>
        </div>
        <div class="login-form">
            <div class="login-content">
                <div class="form-login-error">
                    <h3>@lang('auth.failed')</h3>
                </div>
                <form method="post" id="form_login">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="entypo-user"></i>
                            </div>
                            <input class="form-control" name="email" id="email" placeholder="Username or Email" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="entypo-key"></i>
                            </div>
                            <input type="password" class="form-control" name="password" id="password"
                                   placeholder="Password" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-block btn-login">
                            <i class="entypo-login"></i>
                            Login In
                        </button>
                    </div>
                    <div class="login-bottom-links">
                        <a href="{{url('password/reset')}}">Forgot your password? </a> <br>
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