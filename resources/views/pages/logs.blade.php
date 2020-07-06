@extends('layouts.dashboard', ['current_page'=>'Reports', 'title'=>'Reports'])

@section('content')
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div id="jqxgrid_messages"></div>
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
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxdata.export.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/jqxgrid.export.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/globalization/globalize.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/jqwidgets/globalization/translations.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var source =
                {
                    datatype: "json",
                    datafields: [
                        {name: 'id', type: 'integer'},
                        {name: 'sender', type: 'string'},
                        {name: 'receiver', type: 'string'},
                        {name: 'text', type: 'string'},
                        {name: 'mms', type: 'string'},
                        {name: 'direction', type: 'string'},
                        {name: 'folder', type: 'string'},
                        {name: 'status', type: 'string'},
                        {name: 'created_at', type: 'date'},
                        {name: 'updated_at', type: 'date'}
                    ],
                    cache: false,
                    url: Config.defaultURL + "/messages/logs",
                    data: {_token: Config.csrfToken},
                    type: 'POST',
                    filter: function (e) {
                        $.each(e, function (k, v) {
                            if (v.datafield === "created_at" || v.datafield === "updated_at") {
                                var filtergroup = new $.jqx.filter();
                                var filtervalue = moment(v.filter.getfilters()[0].value).format("YYYY-MM-DD HH:mm:ss");
                                var filtercondition = 'GREATER_THAN_OR_EQUAL';
                                var filter1 = filtergroup.createfilter('datefilter', filtervalue, filtercondition);
                                filtervalue = moment(v.filter.getfilters()[1].value).format("YYYY-MM-DD HH:mm:ss");
                                filtercondition = 'LESS_THAN_OR_EQUAL';
                                var filter2 = filtergroup.createfilter('datefilter', filtervalue, filtercondition);
                                v.filter.clear();
                                v.filter.addfilter(0, filter1);
                                v.filter.addfilter(0, filter2);
                            }
                        });
                        messages.jqxGrid('updatebounddata', 'filter');
                    },
                    sort: function () {
                        messages.jqxGrid('updatebounddata', 'sort');
                    },
                    root: 'Rows',
                    beforeprocessing: function (data) {
                        if (data !== null) {
                            source.totalrecords = data.TotalRows;
                        }
                    }
                };
            var dataadapter = new $.jqx.dataAdapter(source, {
                    loadError: function (xhr, status, error) {
                        console.log(error);
                    }
                }
            );


            function addFilters() {
                var filtergroup = new $.jqx.filter();
                var filtervalue = '{{\Carbon\Carbon::now()->startOfDay()}}';
                var filtercondition = 'GREATER_THAN_OR_EQUAL';
                var filter1 = filtergroup.createfilter('datefilter', filtervalue, filtercondition);
                filtervalue = '{{\Carbon\Carbon::now()->endOfDay()}}';
                filtercondition = 'LESS_THAN_OR_EQUAL';
                var filter2 = filtergroup.createfilter('datefilter', filtervalue, filtercondition);
                filtergroup.addfilter(0, filter1);
                filtergroup.addfilter(0, filter2);
                return filtergroup;
            }

            var messages = $("#jqxgrid_messages").jqxGrid(
                {
                    source: dataadapter,
                    filterable: true,
                    width: '100%',
                    height: $(window).height() - 175,
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
                    editable: false,
                    enablebrowserselection: true,
                    enablehover: false,
                    selectionmode: 'checkbox',
                    theme: 'metro',
                    rendergridrows: function (obj) {
                        return obj.data;
                    },
                    showtoolbar: true,
                    rendertoolbar: function (toolbar) {
                        var container = $("<div style='margin: 5px;'></div>");
                        toolbar.append(container);
                        @if(Auth::user()->can('messages.delete'))
                        container.append('<button class="btn btn-danger btn-sm btn-icon icon-left" id="delete_log" style="margin-left: 10px;display: none"><i class="entypo-trash"></i>Delete</button>');
                        @endif
                        container.append('<button class="btn btn-info btn-sm btn-icon icon-left export" data-format="csv" style="margin-left: 10px"><i class="entypo-down"></i>Export To CSV</button>');
                        container.append('<button class="btn btn-info btn-sm btn-icon icon-left export" data-format="xls" style="margin-left: 10px"><i class="entypo-down"></i>Export To Excel</button>');
                        $(".export").on('click', function () {
                            messages.jqxGrid('exportdata', $(this).data('format'), 'Logs');
                        });
                        $("#delete_log").on('click', function () {
                            var del_id = [];
                            $.each(messages.jqxGrid('getselectedrowindexes'), function (k, v) {
                                var row = messages.jqxGrid('getrowdata', v);
                                del_id.push(row.id);
                            });
                            BootstrapDialog.confirm({
                                title: 'Message Log Delete',
                                message: 'Do you really want to delete log?',
                                type: BootstrapDialog.TYPE_DANGER,
                                closable: true,
                                draggable: false,
                                btnCancelLabel: 'Close',
                                btnOKLabel: 'Delete',
                                btnOKClass: 'btn-danger',
                                callback: function (result) {
                                    if (result) {
                                        $.post(Config.defaultURL + "/messages/logs", {
                                            _method: 'DELETE',
                                            id: del_id
                                        }, function () {
                                            if (messages.jqxGrid('getdatainformation').rowscount <= del_id.length) {
                                                $('#delete_log').hide();
                                            }
                                            messages.jqxGrid('clearselection');
                                            messages.jqxGrid('updatebounddata');
                                        }, "json");
                                    }
                                }
                            });
                        });
                    },
                    columns: [
                        {text: 'Sender', datafield: 'sender'},
                        {text: 'Receiver', datafield: 'receiver'},
                        {text: 'Text', datafield: 'text'},
                        {
                            text: 'MMS', datafield: 'mms', cellsrenderer: function (row, datafield, value) {
                            if (value !== "") {
                                return "<div style=\"margin-top: 6px;\" class=\"jqx-grid-cell-left-align\"><a href=\"" + value + "\" target='_blank'>Open</a></div>";
                            }
                            return "";
                        }
                        },
                        {
                            text: 'Direction',
                            datafield: 'direction',
                            filtertype: 'list',
                            filteritems: ["inbound", "outbound"]
                        },
                        {
                            text: 'Folder',
                            datafield: 'folder',
                            filtertype: 'list',
                            filteritems: ["chat", "auto-reply", "forwards", "appointments", "archive", "trashed"]
                        },
                        {
                            text: 'Status',
                            datafield: 'status',
                            filtertype: 'list',
                            filteritems: ["chat", "pending", "sent", "received", "failed","canceled"]
                        },
                        {
                            text: 'Created At',
                            datafield: 'created_at',
                            cellsformat: 'yyyy-MM-dd HH:mm:ss',
                            filtertype: 'range',
                            filter: addFilters()
                        },
                        {
                            text: 'Updated At',
                            datafield: 'updated_at',
                            cellsformat: 'yyyy-MM-dd HH:mm:ss',
                            filtertype: 'range'
                        }
                    ]
                }).on('rowselect', function (event) {
                $('#delete_log').toggle(messages.jqxGrid('getselectedrowindexes').length > 0);
            }).on('rowunselect', function (event) {
                $('#delete_log').toggle(messages.jqxGrid('getselectedrowindexes').length > 0);
            });
        });
    </script>
@endpush
