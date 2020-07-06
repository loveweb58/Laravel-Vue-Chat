@extends('layouts.dashboard', ['current_page'=>'503', 'title'=>'503'])

@section('content')
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="page-error-404">
                <div class="error-symbol">
                    <i class="entypo-attention"></i>
                </div>
                <div class="error-text">
                    <h2>503</h2>
                    <p>დაფიქსირდა შეცდომა!</p>
                </div>
                <hr/>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
