<!DOCTYPE html>
<html lang="ka">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content="SMS Portal"/>
    <meta name="author" content=""/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="refresh" content="{{(config('session.lifetime', 60) * 60)+60}}"/>
    <link rel="icon" type="image/png" href="{{asset("assets/images/favicon.ico")}}" sizes="16x16"/>
    <title>{{ config('app.name', 'Laravel') }} | {{$title or 'Dashboard'}}</title>
    <link rel="stylesheet" href="{{asset('assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/font-icons/entypo/css/entypo.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/font-icons/font-awesome/css/font-awesome.css')}}">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/neon-core.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/neon-theme.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/neon-forms.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/bpg-nino-mtavruli.css')}}">
    <link rel="stylesheet" href="{{asset('assets/js/daterangepicker/daterangepicker-bs3.css')}}">
    <!-- Select2 -->
    <link href="{{asset('assets/js/select2/select2-bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('assets/js/select2/select2.css')}}" rel="stylesheet">
    <link href="{{asset('assets/js/selectboxit/jquery.selectBoxIt.css')}}" rel="stylesheet">
    <link href="{{asset('assets/js/parsleyjs/src/parsley.css')}}" rel="stylesheet">
    <script src="{{asset('assets/js/jquery-1.11.3.min.js')}}"></script>
    <script>
        var Config = {
            defaultURL: '{{url('/')}}',
            dashboardURL: '{{url('dashboard')}}',
            url: '{{Request::url()}}',
            csrfToken: '{{csrf_token()}}',
            defaultTitle: '{{ config('app.name', 'Laravel') }} | {{$title or 'Dashboard'}}',
        };
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': Config.csrfToken}
        });
    </script>

    <!--[if lt IE 9]>
    <script src="{{asset('assets/js/ie8-responsive-file-warning.js')}}"></script><![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href="{{asset("assets/plugins/bootstrap3-dialog/bootstrap-dialog.min.css")}}" rel="stylesheet">
    @stack('styles')

</head>
<body class="page-body{{$body_class or ''}}" data-url="{{request()->url()}}" onload="show_loading_bar(100);">
@yield('body')
<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{asset('assets/js/jvectormap/jquery-jvectormap-1.2.2.css')}}">
<link rel="stylesheet" href="{{asset('assets/js/rickshaw/rickshaw.min.css')}}">

<script>
    $('#main-menu')
        .find('a[href="' + Config.url + '"]')
        .parent('li')
        .addClass('active')
        .parents('ul')
        .parent('li')
        .addClass('opened');
</script>

<!-- Bottom scripts (common) -->
<script src="{{asset('assets/js/gsap/TweenMax.min.js')}}"></script>
<script src="{{asset('assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.js')}}"></script>
<script src="{{asset('assets/js/joinable.js')}}"></script>
<script src="{{asset('assets/js/resizeable.js')}}"></script>
<script src="{{asset('assets/js/neon-api.js')}}"></script>
<script src="{{asset("assets/plugins/jquery-form/jquery.form.js")}}"></script>
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.inputmask.bundle.js')}}"></script>
<script src="{{asset('assets/js/fileinput.js')}}"></script>
<!-- Parsley -->
<script src="{{asset('assets/js/parsleyjs/dist/parsley.min.js')}}"></script>
<script src="{{asset('assets/js/parsleyjs/dist/i18n/'.app()->getLocale().'.js')}}"></script>
<script src="{{asset('assets/js/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-tagsinput.min.js')}}"></script>
<script src="{{asset('assets/js/typeahead.min.js')}}"></script>
<script src="{{asset('assets/js/selectboxit/jquery.selectBoxIt.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.multi-select.js')}}"></script>
<script src="{{asset('assets/js/zurb-responsive-tables/responsive-tables.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-switch.min.js')}}"></script>
<script src="{{asset('assets/js/neon-demo.js')}}"></script>
<script src="{{asset('assets/js/notifications.js')}}"></script>
<script src="{{asset("assets/plugins/bootstrap3-dialog/bootstrap-dialog.min.js")}}"></script>
<script src="{{asset("assets/plugins/moment-js/moment.min.js")}}"></script>
<script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-timepicker.min.js')}}"></script>
<script src="{{asset('assets/js/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('assets/js/neon-custom.js')}}"></script>
<script src="{{asset("assets/plugins/jquery-ajax-form/ajax-form.js")}}"></script>
<script>
    moment.locale('{{app()->getLocale()}}')
</script>
<!-- Imported scripts on this page -->
@stack('scripts')
</body>
</html>