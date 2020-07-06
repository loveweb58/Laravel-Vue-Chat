@extends('layouts.app')

@section('body')
    <div class="page-container {{Auth::user()->getSetting('sidebar_menu_state')}}">
        <!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
        <div class="sidebar-menu">
            <div class="sidebar-menu-inner">
                <header class="logo-env">
                    <!-- logo -->
                    <div class="logo">
                        <a href="{{url('dashboard')}}">
                            <img src="{{ asset('/assets/images/logo.svg') }}" width="120" alt=""/>
                        </a>
                    </div>
                    <!-- logo collapse icon -->
                    <div class="sidebar-collapse">
                        <a href="#" class="sidebar-collapse-icon with-animation">
                            <!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
                            <i class="entypo-menu"></i>
                        </a>
                    </div>
                    <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
                    <div class="sidebar-mobile-menu visible-xs">
                        <a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
                            <i class="entypo-menu"></i>
                        </a>
                    </div>
                </header>
                <ul id="main-menu" class="main-menu auto-inherit-active-class">
                    <!-- add class "multiple-expanded" to allow multiple submenus to open -->
                    <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
                    <li>
                        <a href="{{url('/')}}">
                            <i class="entypo-home"></i>
                            <span class="title">Dashboard</span>
                        </a>
                    </li>
                    @if(Auth::user()->can(['contacts.view', 'groups.view']))
                        <li class="has-sub">
                            <a href="{{url('#')}}">
                                <i class="entypo-database"></i>
                                <span class="title">Data</span>
                            </a>
                            <ul>
                                @if(Auth::user()->can('contacts.view'))
                                    <li>
                                        <a href="{{url('contacts')}}">
                                            <span class="title">Contacts</span>
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->can('groups.view'))
                                    <li>
                                        <a href="{{url('groups')}}">
                                            <span class="title">Groups</span>
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->can('custom_labels.view') && Auth::user()->account->limits('custom_labels')> 0)
                                    <li>
                                        <a href="{{url('custom-labels')}}">
                                            <span class="title">Custom Labels</span>
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->can('message_templates.view') && Auth::user()->account->limits('message_templates')> 0)
                                    <li>
                                        <a href="{{url('message-templates')}}">
                                            <span class="title">Message Templates</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->can('appointments.view'))
                        <li>
                            <a href="{{url('appointments')}}">
                                <i class="entypo-newspaper"></i>
                                <span class="title">Appointments</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->can('auto_reply.view'))
                        <li>
                            <a href="{{url('auto-reply')}}">
                                <i class="entypo-shareable"></i>
                                <span class="title">Auto Reply</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->can('forwarding.view'))
                        <li>
                            <a href="{{url('forwards')}}">
                                <i class="entypo-forward"></i>
                                <span class="title">SMS Forwarding</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->can('schedule.view'))
                        <li>
                            <a href="{{url('schedule')}}">
                                <i class="entypo-calendar"></i>
                                <span class="title">Recurring</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->can('messages.logs'))
                        <li>
                            <a href="{{url('messages/logs')}}">
                                <i class="entypo-doc-text"></i>
                                <span class="title">Reports</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->can('admin.reports'))
                        <li>
                            <a href="{{url('reports')}}">
                                <i class="entypo-chart-line"></i>
                                <span class="title">Statistics</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->can(['users.view', 'roles.view']))
                        <li class="has-sub">
                            <a href="{{url('#')}}">
                                <i class="entypo-user"></i>
                                <span class="title">Users</span>
                            </a>
                            <ul>
                                @if(Auth::user()->can('roles.view'))
                                    <li>
                                        <a href="{{url('roles')}}">
                                            <span class="title">Roles</span>
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->can('users.view'))
                                    <li>
                                        <a href="{{url('users')}}">
                                            <span class="title">Users</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->can('blacklist.view'))
                        <li>
                            <a href="{{url('blacklist')}}">
                                <i class="entypo-block"></i>
                                <span class="title">Black List</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->can('accounts.view'))
                        <li>
                            <a href="{{url('accounts')}}">
                                <i class="entypo-tools"></i>
                                <span class="title">Accounts</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->can('docs.view'))
                        <li>
                            <a href="{{url('/docs')}}" target="_blank">
                                <i class="entypo-code"></i>
                                <span class="title">For Developers</span>
                            </a>
                        </li>
                    @endif
                    @if(session()->has('impersonate_user'))
                        <li>
                            <a href="{{url('accounts/logout')}}">
                                <i class="entypo-lock"></i>
                                <span class="title">Return to Admin Account</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="main-content">
            <div class="row">
                <!-- Profile Info and Notifications -->
                <div class="col-md-6 col-sm-4 clearfix hidden-xs">
                    <ol class="breadcrumb bc-3">
                        @if(isset($current_page) && $current_page != 'Dashboard')
                            <li>
                                <a href="{{url('dashboard')}}"><i class="fa fa-home"></i>Dashboard</a>
                            </li>
                            @foreach($pageMenu ?? [] as $k=>$v)
                                <li>
                                    <a href="{{url($v['url'])}}">{{$v['name']}}</a>
                                </li>
                            @endforeach
                            <li class="active">
                                <strong>{{$current_page}}</strong>
                            </li>
                        @else
                            <li class="active">
                                <i class="fa fa-home"></i><strong>Dashboard</strong>
                            </li>
                        @endif
                    </ol>
                </div>
                <!-- Raw Links -->
                <div class="col-md-6 col-sm-8 clearfix">
                    <ul class="user-info pull-right pull-none-xsm">
                        <!-- Profile Info -->
                        <li class="profile-info pull-right dropdown">
                            <!-- add class "pull-right" if you want to place this from right -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{url(Auth::user()->avatar ?? "/assets/images/member.jpg")}}" alt="" class="img-circle" width="44"/>
                                {{Auth::user()->first_name . ' ' . Auth::user()->last_name}}
                            </a>
                            <ul class="dropdown-menu">
                                <!-- Reverse Caret -->
                                <li class="caret"></li>
                                <li>
                                    <a href="{{url('settings')}}">
                                        <i class="entypo-user"></i>
                                        Settings
                                    </a>
                                </li>
                                <li>
                                    <a href="{{url('logout')}}">
                                        <i class="entypo-logout"></i>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="user-info pull-right pull-right-xs pull-none-xsm hidden-xs" id="notifications">
                        <li class="notifications pull-right dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                               data-close-others="true">
                                <i class="entypo-comment"></i>
                                <span class="badge badge-info" data-variables="counter"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <ul class="dropdown-menu-list scroller" data-variables="notifications">
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="user-info pull-right pull-right-xs pull-none-xsm">
                        <li class="notifications pull-right dropdown">
                            <a href="#" onclick="vueChat.$refs.chat.toggle()" data-toggle="chat">
                                <i class="entypo-chat"></i>
                                <span class="badge badge-success chat-notifications-badge is-hidden" id="chat-total-unread-messages">0</span>
                            </a>
                        </li>
                    </ul>
                    <ul class="list-inline links-list pull-right">
                        <li class="profile-info dropdown">
                            <a href="#" data-toggle="dropdown">
                                <i class="entypo-mail"></i>
                                Send SMS
                            </a>
                            <ul class="dropdown-menu">
                                <li class="caret"></li>
                                @if( Auth::user()->account->messagesSingle()->count() < Auth::user()->account->limits('single_sms'))
                                <li>
                                    <a href="#sms-single" data-toggle="modal">
                                        <i class="entypo-user"></i>
                                        To numbers
                                    </a>
                                </li>
                                @else
                                <li>Single SMS limit</li>
                                @endif
                                @if( Auth::user()->account->messagesGroup()->count() < Auth::user()->account->limits('group_sms'))
                                    @if(Auth::user()->account->limits('single_only', false))
                                    
                                    @else
                                    <li>
                                        <a href="#sms-groups" data-toggle="modal">
                                            <i class="entypo-mail"></i>
                                            To groups
                                        </a>
                                    </li>
                                    @endif
                                @else
                                <li>Group SMS limit</li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <br/>
            @yield('content')
            <br/>
            <!-- Footer -->
            <footer class="main">
                &copy; 2010-{{date('Y')}} <strong><a href="http://textmymainnumber.com" target="_blank">Text My Main
                                                                                                        Number</a></strong>
                SMS Portal. All Rights Reserved.
            </footer>
        </div>
        <div id="chat" class="fixed" style="z-index: 1000">
            <chat-widget ref="chat"></chat-widget>
        </div>
    </div>
    <div class="modal fade" id="edit-contact">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Contact</h4>
                </div>
                <div class="modal-body">
                    <form class="validate ajax-form" action="{{url("messages/contact/id")}}" method="post" enctype="multipart/form-data">
                        <div class="alert alert-success" style="display: none">
                        </div>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <img src="" class="avatar" style="height: 126px;width: 128px;" onclick="$(this).next('input[name=avatar]').click();">
                                    <input type="file" name="avatar" style="display:none" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name" class="control-label">First Name</label>
                                    <input name="first_name" class="form-control" id="first_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name" class="control-label">Last Name</label>
                                    <input name="last_name" class="form-control" id="last_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone" class="control-label">Phone</label>
                                    <input id="phone" name="phone" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email" class="control-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="website" class="control-label">Website</label>
                                    <input id="website" name="website" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company" class="control-label">Company</label>
                                    <input name="company" class="form-control" id="company">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="position" class="control-label">Position</label>
                                    <input name="position" class="form-control" id="position">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country" class="control-label">Country</label>
                                    <input name="country" class="form-control" id="country">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state" class="control-label">State</label>
                                    <input name="state" class="form-control" id="state">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city" class="control-label">City</label>
                                    <input name="city" class="form-control" id="city">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="address" class="control-label">Address</label>
                                    <input name="address" class="form-control" id="address">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender" class="control-label">Gender</label>
                                    <select id="gender" name="gender" class="selectboxit form-control">
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="birth_date" class="control-label">Date of Birth</label>
                                    <input class="form-control datepicker" id="birth_date" name="birth_date" data-start-view="2" data-format="yyyy-mm-dd">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bd_text" class="control-label">Birthday Text</label>
                                    <textarea rows="1" name="bd_text" class="form-control autogrow" id="bd_text" style="resize: vertical"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description" class="control-label">Description</label>
                                    <textarea rows="1" name="description" class="form-control autogrow" id="description" style="resize: vertical"></textarea>
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
    @if(Auth::user()->can('messages.send'))
        @include('components.popups.sms')
    @endif
@endsection
@push('styles')
    <style>
        .datepicker.datepicker-dropdown {
            z-index: 19000 !important;
        }

        .bootstrap-timepicker-widget.dropdown-menu.open {
            z-index: 19000 !important;
        }

        .select2-drop {
            z-index: 100000 !important;
        }
    </style>
@endpush
@push('scripts')
    <script>
        Object.assign(Config, {
            user: {
                id: '{{Auth::user()->id}}'
            },
            activeConversationsLimit: parseInt('{{Auth::user()->account->limits('conversations_limit', 1)}}'),
            chatMessageTemplates: {{Auth::user()->account->limits('chat_message_templates', false) ? 'true' : 'false'}},
            multipleChatUsers: {{Auth::user()->account->limits('multiple_users_in_chat', false) ? 'true' : 'false'}},
            conversationResize: {{Auth::user()->account->limits('conversation_resize', false) ? 'true' : 'false'}},
            messageTemplates: {!! json_encode(Auth::user()->account->messageTemplates, JSON_PRETTY_PRINT) !!}
        });

        var titleTimer;

        function animateTitle(text, defaultText, interval) {
            titleTimer = setTimeout(function () {
                if (document.title !== defaultText) {
                    document.title = defaultText;
                } else {
                    document.title = text;
                }
                animateTitle(text, defaultText, interval);
            }, interval);
        }

        function sha1(msg) {
            function rotate_left(n, s) {
                var t4 = (n << s) | (n >>> (32 - s));
                return t4;
            };

            function lsb_hex(val) {
                var str = "";
                var i;
                var vh;
                var vl;
                for (i = 0; i <= 6; i += 2) {
                    vh = (val >>> (i * 4 + 4)) & 0x0f;
                    vl = (val >>> (i * 4)) & 0x0f;
                    str += vh.toString(16) + vl.toString(16);
                }
                return str;
            };

            function cvt_hex(val) {
                var str = "";
                var i;
                var v;
                for (i = 7; i >= 0; i--) {
                    v = (val >>> (i * 4)) & 0x0f;
                    str += v.toString(16);
                }
                return str;
            };

            function Utf8Encode(string) {
                string = string.replace(/\r\n/g, "\n");
                var utftext = "";
                for (var n = 0; n < string.length; n++) {
                    var c = string.charCodeAt(n);
                    if (c < 128) {
                        utftext += String.fromCharCode(c);
                    }
                    else if ((c > 127) && (c < 2048)) {
                        utftext += String.fromCharCode((c >> 6) | 192);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }
                    else {
                        utftext += String.fromCharCode((c >> 12) | 224);
                        utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }
                }
                return utftext;
            };
            var blockstart;
            var i, j;
            var W = new Array(80);
            var H0 = 0x67452301;
            var H1 = 0xEFCDAB89;
            var H2 = 0x98BADCFE;
            var H3 = 0x10325476;
            var H4 = 0xC3D2E1F0;
            var A, B, C, D, E;
            var temp;
            msg = Utf8Encode(msg);
            var msg_len = msg.length;
            var word_array = new Array();
            for (i = 0; i < msg_len - 3; i += 4) {
                j = msg.charCodeAt(i) << 24 | msg.charCodeAt(i + 1) << 16 |
                    msg.charCodeAt(i + 2) << 8 | msg.charCodeAt(i + 3);
                word_array.push(j);
            }
            switch (msg_len % 4) {
                case 0:
                    i = 0x080000000;
                    break;
                case 1:
                    i = msg.charCodeAt(msg_len - 1) << 24 | 0x0800000;
                    break;
                case 2:
                    i = msg.charCodeAt(msg_len - 2) << 24 | msg.charCodeAt(msg_len - 1) << 16 | 0x08000;
                    break;
                case 3:
                    i = msg.charCodeAt(msg_len - 3) << 24 | msg.charCodeAt(msg_len - 2) << 16 | msg.charCodeAt(msg_len - 1) << 8 | 0x80;
                    break;
            }
            word_array.push(i);
            while ((word_array.length % 16) != 14) word_array.push(0);
            word_array.push(msg_len >>> 29);
            word_array.push((msg_len << 3) & 0x0ffffffff);
            for (blockstart = 0; blockstart < word_array.length; blockstart += 16) {
                for (i = 0; i < 16; i++) W[i] = word_array[blockstart + i];
                for (i = 16; i <= 79; i++) W[i] = rotate_left(W[i - 3] ^ W[i - 8] ^ W[i - 14] ^ W[i - 16], 1);
                A = H0;
                B = H1;
                C = H2;
                D = H3;
                E = H4;
                for (i = 0; i <= 19; i++) {
                    temp = (rotate_left(A, 5) + ((B & C) | (~B & D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }
                for (i = 20; i <= 39; i++) {
                    temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }
                for (i = 40; i <= 59; i++) {
                    temp = (rotate_left(A, 5) + ((B & C) | (B & D) | (C & D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }
                for (i = 60; i <= 79; i++) {
                    temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }
                H0 = (H0 + A) & 0x0ffffffff;
                H1 = (H1 + B) & 0x0ffffffff;
                H2 = (H2 + C) & 0x0ffffffff;
                H3 = (H3 + D) & 0x0ffffffff;
                H4 = (H4 + E) & 0x0ffffffff;
            }
            var temp = cvt_hex(H0) + cvt_hex(H1) + cvt_hex(H2) + cvt_hex(H3) + cvt_hex(H4);

            return temp.toLowerCase();
        }
    </script>
    <script src="{{asset("assets/js/smsLength.js")}}"></script>
    <script src="{{asset('assets/js/toastr.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    <!--suppress EqualityComparisonWithCoercionJS -->
    <script>
        $(function () {
            $.post(Config.defaultURL + "/messages/last-messages", {}, function (data) {
                $.each(data, function (k, v) {
                    neonNotifications.add(null, v.chat_user + ' : ' + v.text, v.created_at, false, '#');
                });
            }, "json");
        });
        document.addEventListener('DOMContentLoaded', function () {
            if (Notification.permission !== "granted")
                Notification.requestPermission();
        });

        function notifyBrowser(title, text, chat_user, avatar, conversation) {
            if (!Notification) {
                alert('Desktop notifications not available in your browser. Try Chromium.');
                return;
            }

            if (Notification.permission !== "granted")
                Notification.requestPermission();
            else {
                let notification = new Notification(title, {
                    icon: avatar,
                    body: text
                });

                notification.onclick = function () {
                    vueChat.$refs.chat.openConversation(conversation.id);
                    notification.close();
                };
            }
        }

        function clearFields(form) {
            form.find('input[type!=hidden]')
                .not('.datepicker')
                .not('.timepicker')
                .val('')
                .end()
                .find('textarea')
                .val('');
            window.setTimeout(function () {
                form.find('.alert').fadeOut();
            }, 2000);
        }

        $(function () {
            $('body').on('hidden.bs.modal', function () {
                $('input[data-close-select2]').select2("close");
                $('select[data-close-select2]').select2("close");
            }).on('click', function () {
                $('input[data-close-select2]').select2("close");
                $('select[data-close-select2]').select2("close");
            }).on('#edit-contact').on('hidden.bs.modal', function () {
                if ($(this).find('form').data('updated')) {
                    window.location.reload(true);
                }
            }).find('input[name=avatar]').change(function () {
                readURL(this);
            });

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $(input).prev('.avatar').attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("form[data-action=confirm]").on('submit', function (e) {
                e.preventDefault();
                var form = this;
                BootstrapDialog.confirm({
                    title: 'Action Confirmation',
                    message: 'Do you really want to perform this action?',
                    type: BootstrapDialog.TYPE_DANGER,
                    closable: true,
                    draggable: false,
                    btnCancelLabel: 'Close',
                    btnOKLabel: 'Confirm',
                    btnOKClass: 'btn-danger',
                    callback: function (result) {
                        if (result) {
                            form.submit();
                        }
                    }
                });
            });
        });

    </script>
@endpush