@extends('layouts.dashboard', ['current_page'=>'Dashboard', 'title'=>'Dashboard'])

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="tile-stats tile-red">
                <div class="icon"><i class="entypo-mail"></i></div>
                <div class="num" data-start="0" data-end="{{$account->limits('monthly_sms')}}"
                     data-duration="1500" data-delay="0">0
                </div>
                <h3>SMS</h3>
                <p>SMS Monthly Limit</p>
            </div>
        </div>
        <div class="clear visible-xs"></div>
        <div class="col-md-3">
            <div class="tile-stats tile-aqua">
                <div class="icon"><i class="entypo-user"></i></div>
                <div class="num" data-start="0"
                     data-end="{{$account->limits('users')}}"
                     data-postfix="" data-duration="1500" data-delay="600">0
                </div>
                <h3>Users</h3>
                <p>Users Limit</p>
            </div>
        </div>
        <div class="clear visible-xs"></div>
        <div class="col-md-3">
            <div class="tile-stats tile-aqua">
                <div class="icon"><i class="entypo-users"></i></div>
                <div class="num" data-start="0"
                     data-end="{{$account->limits('groups')}}"
                     data-postfix="" data-duration="1500" data-delay="600">0
                </div>
                <h3>Groups</h3>
                <p>Groups Limit</p>
            </div>
        </div>
        <div class="clear visible-xs"></div>
        <div class="col-md-3">
            <div class="tile-stats tile-blue">
                <div class="icon"><i class="entypo-rss"></i></div>
                <div class="num" data-start="0"
                     data-end="{{$account->limits('keywords')}}"
                     data-postfix="" data-duration="1500" data-delay="1200">0
                </div>
                <h3>Keywords</h3>
                <p>Keywords Limit</p>
            </div>
        </div>
    </div>
@endsection
@push('styles')
@endpush
@push('scripts')
@endpush
