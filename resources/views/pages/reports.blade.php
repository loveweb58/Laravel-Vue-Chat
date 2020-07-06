@extends('layouts.dashboard', ['current_page'=>'Reports', 'title'=>'Reports'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Usage Charts</div>

                    <div class="panel-options">
                        <a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{url("reports")}}" method="get">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="accounts" class="control-label">Accounts</label>
                                            <select class="form-control select2" id="accounts" name="accounts[]" multiple data-allow-clear="true" data-placeholder="All">
                                                @foreach($accounts as $account)
                                                    <option value="{{$account->id}}" @if(in_array($account->id, $request->input('accounts', []))) selected @endif>{{$account->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="type" class="control-label">Chart Type</label>
                                            <select class="form-control select2" id="type" name="type">
                                                <option value="line" @if($request->input('type', 'line') === "line") selected @endif>
                                                    Line
                                                </option>
                                                <option value="bar" @if($request->input('type', 'line') === "bar") selected @endif>
                                                    Bar
                                                </option>
                                                <option value="area" @if($request->input('type', 'line') === "area") selected @endif>
                                                    Area
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date" class="control-label">Date</label>
                                            <input class="form-control datepicker" id="date" name="date" value="{{$request->input('date', Carbon\Carbon::now()->toDateString())}}" data-format="yyyy-mm-dd">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="control-label"> </label>
                                        <div class="form-group">
                                            <button class="btn btn-success">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            {!! $mUsageChart->html() !!}
                        </div>
                        <div class="col-md-12">
                            {!! $dUsageChart->html() !!}
                        </div>
                        <div class="col-md-12">
                            {!! $hUsageChart->html() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .page-body .select2-container .select2-choice {
            height: 35px;
            line-height: 35px;
            margin: 0
        }
    </style>
    {!! Charts::styles() !!}
@endpush
@push('scripts')
    {!! Charts::scripts() !!}
    {!! $mUsageChart->script() !!}
    {!! $dUsageChart->script() !!}
    {!! $hUsageChart->script() !!}
@endpush
