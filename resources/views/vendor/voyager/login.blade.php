<!doctype html>
<html lang="ar" dir="rtl" class="light-style layout-wide customizer-hide" data-theme="theme-default"
      data-assets-path="../../assets/" data-template="vertical-menu-template-no-customizer" data-style="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title>تسجيل الدخول</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet"/>
    <style>body {
            font-family: 'Tajawal', sans-serif;
        }</style>

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/fontawesome.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/tabler-icons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/flag-icons.css') }}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/rtl/core.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/rtl/theme-default.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/css/demo.css') }}"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/node-waves/node-waves.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/typeahead-js/typeahead.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/@form-validation/form-validation.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/pages/page-auth.css') }}"/>

    <!-- Helpers -->
    <script src="{{ asset('admin-assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('admin-assets/js/config.js') }}"></script>
</head>
<body>
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
            <div class="card">
                <div class="card-body">
                    <div class="app-brand justify-content-center mb-6">
                        <a href="#" class="app-brand-link">
                            <img src="{{ asset('admin-assets/custom/logo-ar.svg') }}" alt="Logo" width="160">
                        </a>
                    </div>

                    <form id="formAuthentication" class="mb-4" action="{{ route('voyager.login') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="mb-6">
                            <label for="email" class="form-label">البريد الإلكتروني أو اسم المستخدم</label>
                            <input type="text" name="email" id="email" value="{{ old('email') }}" class="form-control"
                                   required>
                        </div>
                        <div class="mb-6 form-password-toggle">
                            <label class="form-label" for="password">كلمة المرور</label>
                            <div class="input-group input-group-merge">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                        <div class="my-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                <label class="form-check-label" for="remember">تذكرني</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary w-100" type="submit">تسجيل الدخول</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="{{ asset('admin-assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
<script src="{{ asset('admin-assets/js/main.js') }}"></script>
<script src="{{ asset('admin-assets/js/pages-auth.js') }}"></script>
</body>
</html>
