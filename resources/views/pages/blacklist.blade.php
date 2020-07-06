@extends('layouts.dashboard', ['current_page'=>'Black List', 'title'=>'Black List'])

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
                <div class="panel-heading">
                    <div class="panel-title">
                        @if(Auth::user()->can('blacklist.create'))
                            <a href="#create" data-toggle="modal">
                                <button class="btn btn-success btn-sm btn-icon icon-left">
                                    <i class="entypo-plus"></i>Add
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
                                <th class="column-title">Number</th>
                                <th class="column-title">Created At</th>
                                <th class="column-title no-link last" style="width: 40px">
                                    <span class="nobr">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($numbers as $number)
                                <tr class="even pointer">
                                    <td>{{$number->number}}</td>
                                    <td>{{$number->created_at}}</td>
                                    <td class="last text-center">
                                        @if(Auth::user()->can('blacklist.delete'))
                                            <form style="display: inline-block" method="post" data-action="confirm" action="{{url("blacklist/{$number->id}")}}">
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
                        {{ $numbers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Add Number</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("blacklist")}}" data-callback="clearFields" method="post" enctype="multipart/form-data">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="number" class="control-label">Number</label>
                                    <input type="text" name="number" class="form-control" id="number">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-info">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
@endpush
@push('scripts')
    <script>
        $('#create').on('hidden.bs.modal', function () {
            if ($('#create').find('form').data('updated')) {
                window.location.reload();
            }
        });
    </script>
@endpush
