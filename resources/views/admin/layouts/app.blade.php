<!DOCTYPE html>
<html lang="en" ng-app="{{ config('app.name') }}" lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">
    <head>
        <meta charset="utf-8" />
        <title>@yield('title', config('app.name'))</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <meta name="description" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="ws_url" content="{{ env('WS_URL') }}">
        <meta name="user_id" content="{{ Auth::id() }}">
        <link rel="icon" type="image/x-icon" href="{{asset('assets/admin/img/favicon/favicon.ico')}}" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
        <link rel="stylesheet" href="{{asset('assets/admin/vendor/fonts/boxicons.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/admin/vendor/css/core.css')}}" class="template-customizer-core-css" />
        <link rel="stylesheet" href="{{asset('assets/admin/vendor/css/theme-default.css')}}" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="{{asset('assets/admin/css/demo.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/admin/css/bootstrapDataTable.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/admin/vendor/libs/apex-charts/apex-charts.css')}}" />
        <script src="{{asset('assets/admin/vendor/js/helpers.js')}}"></script>
        <script src="{{asset('assets/admin/js/config.js')}}"></script>
        <link rel="stylesheet" href="{{asset('assets/admin/css/sweet-alert.css')}}" />
        <link rel="stylesheet" href="{{asset('assets/admin/css/mehmaan-theme.css')}}" />
        @yield('style')
        <style>
            
        </style>
        
    </head>
    <body>
       <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                @include('admin.layouts.elements.left_sidebar')
                <div class="layout-page">
                    @include('admin.layouts.elements.header')
                    <div class="content-wrapper">
                        @yield('content')
                        @include('admin.layouts.elements.footer')
                        <div class="content-backdrop fade"></div>
                    </div>
                    @include('admin.layouts.elements.right_sidebar')
                </div>
        
                <script src="{{asset('assets/admin/vendor/libs/jquery/jquery.js')}}"></script>
                <script src="{{asset('assets/admin/vendor/libs/popper/popper.js')}}"></script>
                <script src="{{asset('assets/admin/vendor/js/bootstrap.js')}}"></script>
                <script src="{{asset('assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
                <script src="{{asset('assets/admin/vendor/js/menu.js')}}"></script>
                <script src="{{asset('assets/admin/vendor/libs/apex-charts/apexcharts.js')}}"></script>
                <script src="{{asset('assets/admin/js/main.js')}}"></script>
                <script src="{{asset('assets/admin/js/dataTable.js')}}"></script>
                <script src="{{asset('assets/admin/js/bootstrapDataTable.js')}}"></script>
                <script src="{{asset('assets/admin/js/dashboards-analytics.js')}}"></script>
                <script src="{{asset('assets/admin/js/moment.min.js')}}"></script>
                <script src="{{asset('assets/admin/js/ajax-actions.js')}}"></script>
                <script async defer src="https://buttons.github.io/buttons.js"></script>
                @yield('script')
                @include('admin.layouts.elements.sweet_alerts')
                <script>
                (function() {
                    function loadNotifications() {
                        $.get('{{ route("admin.notifications.recent") }}', function(res) {
                            var list = $('#notification-dropdown-list');
                            list.empty();
                            if (res.notifications.length === 0) {
                                list.html('<p class="text-center text-muted py-3">No notifications</p>');
                            } else {
                                $.each(res.notifications, function(i, n) {
                                    var dot = n.is_read ? '' : 'border-start border-primary border-3';
                                    list.append('<a href="' + (n.url || '#') + '" class="d-block py-2 px-1 text-decoration-none ' + dot + ' border-bottom" onclick="$.post(\'' + '{{ route("admin.notifications.mark-read", ":id") }}'.replace(':id', n.id) + '\')">' +
                                        '<div class="fw-bold small">' + n.title + '</div>' +
                                        '<div class="text-muted" style="font-size:12px">' + n.message + '</div>' +
                                        '<div class="text-muted" style="font-size:11px">' + new Date(n.created_at).toLocaleString() + '</div>' +
                                        '</a>');
                                });
                            }
                        });
                        $.get('{{ route("admin.notifications.unread-count") }}', function(res) {
                            var badge = $('.badge-notif-count');
                            if (res.count > 0) { badge.text(res.count).show(); } else { badge.hide(); }
                        });
                    }
                    loadNotifications();
                    setInterval(loadNotifications, 60000);
                })();
                </script>
            </div>
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
    </body>
</html>