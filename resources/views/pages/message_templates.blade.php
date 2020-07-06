@extends('layouts.dashboard', ['current_page'=>'Message Templates', 'title'=>'Message Templates'])

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
                <div class="panel-heading">
                    <div class="panel-title">
                        @if(Auth::user()->can('message_templates.create'))
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
                                <th class="column-title">Text</th>
                                <th class="column-title">Created At</th>
                                <th class="column-title">Updated At</th>
                                <th class="column-title no-link last" style="width: 90px">
                                    <span class="nobr">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($templates as $template)
                                <tr class="even pointer">
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$template->name}}</td>
                                    <td>{{$template->text}}</td>
                                    <td>{{$template->created_at}}</td>
                                    <td>{{$template->updated_at}}</td>
                                    <td class="last text-center">
                                        @if(Auth::user()->can('message_templates.update'))
                                            <a class="item-edit" data-id="{{$template->id}}">
                                                <button class="btn btn-info btn-xs" title="Edit">
                                                    <i class="entypo-pencil"></i>
                                                </button>
                                            </a>
                                        @endif
                                        @if(Auth::user()->can('message_templates.delete'))
                                            <form style="display: inline-block" data-action="confirm" method="post" action="{{url('message-templates/'.$template->id)}}">
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
                        {{ $templates->links() }}
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
                    <h4 class="modal-title">Create Message Template</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("message-templates")}}" method="post" data-callback="clearFields">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">Name</label>
                                    <input name="name" class="form-control" id="name">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="text" class="control-label">Text</label>
                                    <textarea rows="1" name="text" class="form-control autogrow" id="text" style="resize: vertical"></textarea>
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
                    <h4 class="modal-title">Edit Message Template</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("message-templates/id")}}" method="post">
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
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="text" class="control-label">Text</label>
                                    <textarea rows="1" name="text" class="form-control autogrow" id="text" style="resize: vertical"></textarea>
                                </div>
                            </div>
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
    </style>
@endpush
@push('scripts')
    <script>
        $(function () {
            $('.item-edit').on('click', function () {
                $.get(Config.defaultURL + "/message-templates/" + $(this).data('id') + "/edit", function (data) {
                    var edit = $('#edit');
                    edit.find('form')
                        .data('updated', false).attr('action', Config.defaultURL + '/message-templates/' + data.id)
                        .end()
                        .find('.alert')
                        .hide();
                    jQuery.each(data, function (k, v) {
                        edit.find('[name=' + k + ']').val(v);
                        if (jQuery.isArray(v)) {
                            edit.find('select[name=' + k + '\\[\\]]').val(v).trigger("change");
                        }
                    });
                    $.each(edit.find(".autogrow"), function (k, v) {
                        $(v).trigger('input');
                    });
                    edit.modal('show', {backdrop: 'static'});
                }, "json");
            });
            $('#edit,#create').on('hidden.bs.modal', function () {
                if ($(this).find('form').data('updated')) {
                    window.location.reload(true);
                }
            });
        });


    </script>
@endpush