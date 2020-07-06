@extends('layouts.dashboard', ['body_class' => ' login-page is-lockscreen login-form-fall'])

@section('body')
    <div class="login-container">

        <div class="login-header login-caret">

            <div class="login-content">

                <a href="{{url('/')}}" class="logo">
                    <img src="{{ asset('assets/images/logo.png') }}" width="120" alt=""/>
                </a>

                <p class="description">Please Enter 2FA Code</p>

                <!-- progress bar indicator -->
                <div class="login-progressbar-indicator">
                    <h3>0%</h3>
                    <span>Please Wait...</span>
                </div>
            </div>

        </div>

        <div class="login-form">

            <div class="login-content">
                <form method="post" id="form_lockscreen">

                    <div class="form-group lockscreen-input">

                        <div class="lockscreen-thumb">
                            <img src="{{url("/assets/images/member.jpg")}}" width="140" class="img-circle"/>

                            <div class="lockscreen-progress-indicator">0%</div>
                        </div>

                        <div class="lockscreen-details">
                            <h4>{{Auth::user()->first_name . ' ' . Auth::user()->last_name}}</h4>
                            <span data-login-text="logging in...">{{Auth::user()->email}}</span>
                        </div>

                    </div>

                    <div class="form-login-error">
                        <h3>2FA Password Is Invalid</h3>
                    </div>

                    <div class="form-group">

                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="entypo-key"></i>
                            </div>

                            <input type="password" class="form-control" name="password" id="password" placeholder="code" autocomplete="off"/>
                        </div>

                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary btn-block btn-login">
                            <i class="entypo-login"></i>
                            Confirm
                        </button>
                    </div>

                </form>


                <div class="login-bottom-links">

                    <a href="{{url('/logout')}}" class="link">Use Another User
                        <i class="entypo-right-open"></i></a>

                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('assets/js/neon-login.js')}}"></script>
@endpush