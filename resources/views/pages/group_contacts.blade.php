@extends('layouts.dashboard', ['current_page'=>'Members', 'title'=>'Group Members'])

@section('content')
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div id="jqxgrid_contacts"></div>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{asset('assets/jqwidgets/styles/jqx.base.css')}}" type="text/css"/>
    <link rel="stylesheet" href="{{asset('assets/jqwidgets/styles/jqx.metro.css')}}" type="text/css"/>
    <style>
        .select2-drop {
            z-index: 100000 !important;
        }

        .page-body .select2-container .select2-choice {
            height: 23px;
            line-height: 22px;
            margin: 4px
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxcore.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxbuttons.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxscrollbar.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxmenu.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.selection.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.filter.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.sort.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.columnsresize.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxdata.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.edit.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxlistbox.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxcheckbox.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.pager.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxdropdownlist.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxwindow.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxcalendar.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxdatetimeinput.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/globalization/globalize.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/globalization/translations.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var source =
                {
                    datatype: "json",
                    datafields: [
                        {name: 'id', type: 'integer'},
                        {name: 'name', type: 'string'},
                        {name: 'avatar', type: 'string'},
                        {name: 'phone', type: 'string'},
                        {name: 'company', type: 'string'},
                        {name: 'address', type: 'string'},
                        {name: 'email', type: 'string'},
                        {name: 'owned', type: 'bool'}
                    ],
                    cache: false,
                    url: Config.defaultURL + "/groups/{{$group->id}}/contacts",
                    data: {_token: Config.csrfToken},
                    type: 'GET',
                    filter: function () {
                        contacts.jqxGrid('updatebounddata', 'filter');
                    },
                    sort: function () {
                        contacts.jqxGrid('updatebounddata', 'sort');
                    },
                    root: 'Rows',
                    beforeprocessing: function (data) {
                        if (data !== null) {
                            source.totalrecords = data.TotalRows;
                        }
                    },
                    updaterow: function (rowid, rowdata, commit) {
                        columnCheckBoxUpdating = true;
                        $(columnCheckBox).jqxCheckBox({checked: rowdata.owned});
                        if (rowdata.owned) {
                            $.post(Config.defaultURL + "/groups/{{$group->id}}/contacts", {
                                id: rowdata.id
                            }, function () {
                                commit(true);
                                toastr.success('Contact successfully added to group', null, {"timeOut": "1000"});
                            }, "json").fail(function(response){
                                toastr.error('Contacts limit reached', null, {"timeOut": "1000"});
                            });
                        } else {
                            $.post(Config.defaultURL + "/groups/{{$group->id}}/contacts", {
                                _method: 'DELETE',
                                id: rowdata.id
                            }, function () {
                                commit(true);
                                toastr.error('Contact successfully removed from group', null, {"timeOut": "1000"});
                            }, "json");
                        }
                    }
                };
            var dataAdapter = new $.jqx.dataAdapter(source, {
                    loadError: function (xhr, status, error) {
                        console.log(error);
                    },
                    loadComplete: function (data) {
                        var columnCheckBoxStatus = false;
                        $.each(data.Rows, function (k, v) {
                            if (v.owned) {
                                columnCheckBoxStatus = true;
                            }
                        });
                        if (columnCheckBoxStatus) {
                            columnCheckBoxUpdating = true;
                            $(columnCheckBox).jqxCheckBox({checked: columnCheckBoxStatus});
                        }
                    }
                }
            );
            var columnCheckBox = null;
            var columnCheckBoxUpdating = false;

            var contacts = $("#jqxgrid_contacts").jqxGrid(
                {
                    source: dataAdapter,
                    filterable: true,
                    width: '100%',
                    height: $(window).height() - 175 < 300 ? 300 : $(window).height() - 175,
                    sortable: true,
                    autoheight: false,
                    pageable: true,
                    virtualmode: true,
                    pagesize: 100,
                    pagesizeoptions: ['10', '50', '100', '500', '1000'],
                    columnsresize: true,
                    showfilterrow: true,
                    altrows: true,
                    enabletooltips: true,
                    editable: true,
                    selectionmode: 'none',
                    theme: 'metro',
                    rendergridrows: function (obj) {
                        return obj.data;
                    },
                    columns: [
                        {
                            text: '',
                            datafield: 'owned',
                            columntype: 'checkbox',
                            filterable: true,
                            filtertype: 'bool',
                            width: 28,
                            menu: false,
                            sortable: false,
                            renderer: function () {
                                return '<div style="margin-left: 5px; margin-top: 5px;"></div>';
                            },
                            createfilterwidget: function (column, columnelement, widget) {
                                widget.attr('title', 'Filter');
                            },
                            rendered: function (element) {
                                $(element)
                                    .jqxCheckBox({
                                        theme: 'metro',
                                        width: 16,
                                        height: 16,
                                        animationShowDelay: 0,
                                        animationHideDelay: 0
                                    });
                                columnCheckBox = $(element);
                                $(element).on('change', function (event) {
                                    if (columnCheckBoxUpdating) {
                                        columnCheckBoxUpdating = false;
                                        return;
                                    }
                                    var id = [];
                                    $.each(contacts.jqxGrid('getrows'), function (k, v) {
                                        id.push(v.id);
                                    });
                                    $.post(Config.defaultURL + "/groups/{{$group->id}}/contacts", {
                                        _method: 'PUT',
                                        id: id,
                                        status: event.args.checked
                                    }, function () {
                                        contacts.jqxGrid('updatebounddata');
                                        toastr.info('Group contacts successfully updated', null, {"timeOut": "1000"});
                                    }, "json");
                                }).attr('title', 'Select/Deselect All');
                                return true;
                            }
                        },
                        {
                            text: '',
                            datafield: 'avatar',
                            filterable: false,
                            editable: false,
                            width: 30,
                            menu: false,
                            sortable: false,
                            cellsrenderer: function (row, datafield, value) {
                                return "<img src=\"" + value + "\" alt=\"\" class=\"img-circle\" width=\"26\" height=\"26\" style=\"padding: 3px\">";
                            }
                        },
                        {text: 'Phone', datafield: 'phone', cellsalign: 'center', align: 'center', editable: false},
                        {text: 'Name', datafield: 'name', cellsalign: 'center', align: 'center', editable: false},
                        {text: 'Company', datafield: 'company', cellsalign: 'center', align: 'center', editable: false},
                        {text: 'Address', datafield: 'address', cellsalign: 'center', align: 'center', editable: false},
                        {text: 'Email', datafield: 'email', cellsalign: 'center', align: 'center', editable: false}
                    ]
                });
        })
        ;

    </script>
@endpush
