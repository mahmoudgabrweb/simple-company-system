<!doctype html>
<html lang="en" dir="ltr" class="light-style layout-wide customizer-hide" data-theme="theme-default"
      data-assets-path="../../assets/" data-template="vertical-menu-template-no-customizer" data-style="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title>KAME — Login</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/fontawesome.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/tabler-icons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/flag-icons.css') }}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/core.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/theme-default.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/css/demo.css') }}"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/node-waves/node-waves.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/typeahead-js/typeahead.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/@form-validation/form-validation.css') }}"/>

    <style>
        :root{
            --primary:#1D2A3A;
            --accent:#B89564;
            --border:#E0E3E7;
            --muted:#6C757D;
        }
        body { font-family: 'Inter', sans-serif; background:#f8f9fa; }
        .authentication-wrapper { min-height: 100vh; display:flex; align-items:center; justify-content:center; }
        .card { border-radius:.75rem; box-shadow:0 0 20px rgba(0,0,0,.05); border:1px solid var(--border); }
        .btn-primary { background-color:var(--primary); border-color:var(--primary); }
        .btn-primary:hover { background-color:#14202c; border-color:#14202c; }
        .form-label { font-weight:500; color:#1D2A3A; }
        .form-control { height:44px; border-color:var(--border); }
        .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 .2rem rgba(184,149,100,.15); }
        .app-brand img { max-width:160px; }
        .text-muted { color: var(--muted)!important; }
        .input-group-text { cursor: pointer; }
        /* Ensure each field wrapper matches FV's rowSelector */
        .mb-3 { position: relative; }
    </style>

    <!-- Helpers -->
    <script src="{{ asset('admin-assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('admin-assets/js/config.js') }}"></script>
</head>
<body>
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6" style="max-width: 440px; width:100%;">
            <div class="card p-4">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4 text-center">
                        <a href="#" class="app-brand-link d-inline-block">
                            <img src="{{ asset('admin-assets/logo.png') }}" alt="KAME Logo" width="160">
                        </a>
                    </div>

                    <!-- Title -->
                    <h4 class="mb-1 text-center" style="color:#1D2A3A;">Welcome back 👋</h4>
                    <p class="mb-4 text-center text-muted">Please sign in to your KAME account</p>

                    <!-- Alerts -->
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                    @endif
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                    @endif

                    <!-- Login Form -->
                    <form id="formAuthentication" class="mb-4" action="{{ route('voyager.login') }}" method="POST" novalidate>
                        {{ csrf_field() }}

                        <div class="mb-3">
                            <label for="email" class="form-label">Email or Username</label>
                            <input
                                    type="text"
                                    name="email"
                                    id="email"
                                    value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Enter your email or username"
                                    required
                                    autofocus
                            >
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="••••••••"
                                        required
                                >
                                <span class="input-group-text" id="togglePassword">
                                    <i class="ti ti-eye-off"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="#" class="text-muted" style="font-size: .9rem;">Forgot password?</a>
                        </div>

                        <button class="btn btn-primary w-100" type="submit">Sign In</button>
                    </form>

                    <p class="text-center text-muted mb-0" style="font-size: .9rem;">
                        © {{ date('Y') }} <strong style="color:#1D2A3A;">KAME</strong>. All rights reserved.
                    </p>
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

<!-- FormValidation libs (keep) -->
<script src="{{ asset('admin-assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ asset('admin-assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

<!-- IMPORTANT: remove Vuexy’s auto-init that breaks with custom markup -->
{{-- <script src="{{ asset('admin-assets/js/pages-auth.js') }}"></script> --}}

<!-- Small, safe initializer + password toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Password visibility toggle
        const toggle = document.getElementById('togglePassword');
        const pwd = document.getElementById('password');
        if (toggle && pwd) {
            toggle.addEventListener('click', function () {
                const isText = pwd.getAttribute('type') === 'text';
                pwd.setAttribute('type', isText ? 'password' : 'text');
                // swap icon
                this.innerHTML = isText ? '<i class="ti ti-eye-off"></i>' : '<i class="ti ti-eye"></i>';
            });
        }

        // Guard if library missing
        if (!window.FormValidation || !document.getElementById('formAuthentication')) return;

        // Init with correct selectors matching our markup
        FormValidation.formValidation(document.getElementById('formAuthentication'), {
            fields: {
                email: {
                    validators: {
                        notEmpty: { message: 'This field is required' }
                        // If you later enforce email-only, add:
                        // emailAddress: { message: 'Enter a valid email address' }
                    }
                },
                password: {
                    validators: {
                        notEmpty: { message: 'Password is required' },
                        stringLength: { min: 6, message: 'At least 6 characters' }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.mb-3',      // matches our wrappers
                    eleInvalidClass: '',
                    eleValidClass: ''
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                defaultSubmit: new FormValidation.plugins.DefaultSubmit()
            }
        });
    });
</script>
</body>
</html>
