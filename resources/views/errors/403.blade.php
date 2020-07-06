@extends('layouts.dashboard', ['current_page'=>'Forbidden', 'title'=>'Forbidden'])

@section('content')
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="page-error-404">
                <div class="error-symbol">
                    <i class="entypo-attention"></i>
                </div>
                <div class="error-text">
                    <h2>403</h2>
                    <p>Permission Denied!</p>
                </div>
                <hr/>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
