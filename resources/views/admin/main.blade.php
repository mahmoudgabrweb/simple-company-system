<!doctype html>

<html lang="en"
      class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
      dir="ltr"
      data-theme="theme-default"
      data-assets-path="../../admin-assets/"
      data-template="vertical-menu-template-no-customizer"
      data-style="light">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>KAME</title>

    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <meta name="description" content=""/>

    <!-- Favicon -->
{{--    <link rel="icon" type="image/x-icon" href="{{ asset('admin-assets/custom/logo-ar.svg') }}"/>--}}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet"/>

    <!-- Icons (kept EXACTLY as before) -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/fontawesome.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/tabler-icons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/flag-icons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/iconify-icons.css" />') }}"/>
    {{--    <link href="https://unpkg.com/@tabler/icons-webfont@2.41.0/tabler-icons.min.css" rel="stylesheet">--}}

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/rtl/core.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/rtl/theme-default.css') }}"/>

    <link rel="stylesheet" href="{{ asset('admin-assets/css/demo.css') }}"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/node-waves/node-waves.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/typeahead-js/typeahead.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/apex-charts/apex-charts.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}"/>
    <link rel="stylesheet"
          href="{{ asset('admin-assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}"/>
    <link rel="stylesheet"
          href="{{ asset('admin-assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}"/>

    <link rel="stylesheet" href="{{ asset('admin-assets/lib/jquery-confirm.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/lib/toastr.min.css') }}"/>

    <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet"/>

    <link rel="stylesheet" href="{{ asset('admin-assets/css/brand.css') }}"/>
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }

        /* Compact breadcrumbs */
        .cp-breadcrumbs {
            margin: .25rem 0 .5rem;
        }

        .cp-breadcrumbs .breadcrumb {
            margin: 0;
            padding: 0;
        }
    </style>

    @yield('css_sheets')

    <!-- Helpers -->
    <script src="{{ asset('admin-assets/vendor/js/helpers.js') }}"></script>
    <!-- Config -->
    <script src="{{ asset('admin-assets/js/config.js') }}"></script>
</head>

<body>

<?php
if (\Illuminate\Support\Str::startsWith(Auth::user()->avatar, 'http://') || \Illuminate\Support\Str::startsWith(Auth::user()->avatar, 'https://')) {
    $user_avatar = Auth::user()->avatar;
} else {
    $user_avatar = Voyager::image(Auth::user()->avatar);
}
?>
    <!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->
        @include('voyager::dashboard.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->
            @include('voyager::dashboard.navbar')
            <!-- / Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Optional breadcrumbs (inline, compact) -->
                @if (View::hasSection('breadcrumbs') || !empty($breadcrumbs))
                    <div class="container-xxl cp-breadcrumbs">
                        @hasSection('breadcrumbs')
                            @yield('breadcrumbs')
                        @else
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    @foreach ($breadcrumbs as $bc)
                                        @php($label = $bc['label'] ?? $bc['title'] ?? '')
                                        @if (!empty($bc['url']))
                                            <li class="breadcrumb-item"><a href="{{ $bc['url'] }}">{{ $label }}</a></li>
                                        @else
                                            <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                                        @endif
                                    @endforeach
                                </ol>
                            </nav>
                        @endif
                    </div>
                @endif

                <!-- Content -->
                @yield('content')
                <!-- / Content -->

                <!-- Footer -->
                @include('voyager::dashboard.footer')
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js admin-assets/vendor/js/core.js -->

<script type="text/javascript" src="{{ voyager_asset('js/app.js') }}"></script>
@vite('resources/js/app.js')

<script src="{{ asset('admin-assets/lib/jquery-confirm.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    @if (Session::has('alerts'))
    let alerts = {!! json_encode(Session::get('alerts')) !!};
    helpers.displayAlerts(alerts, toastr);
    @endif

    @if (Session::has('message'))
    // TODO: change Controllers to use AlertsMessages trait... then remove this
    var alertType = {!! json_encode(Session::get('alert-type', 'info')) !!};
    var alertMessage = {!! json_encode(Session::get('message')) !!};
    var alerter = toastr[alertType];

    if (alerter) {
        alerter(alertMessage);
    } else {
        toastr.error("toastr alert-type " + alertType + " is unknown");
    }
    @endif
</script>
@stack('javascript')

<script src="{{ asset('admin-assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/js/menu.js') }}"></script>

<!-- Vendors JS -->
<script src="{{ asset('admin-assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

<!-- Flat Picker -->
<script src="{{ asset('admin-assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('admin-assets/js/main.js') }}"></script>

@yield('javascript')
@yield('js_scripts')
</body>

</html>
