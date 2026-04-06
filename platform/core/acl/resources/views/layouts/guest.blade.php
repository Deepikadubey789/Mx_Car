<x-core::layouts.base :body-attributes="['data-bs-theme' => 'light']">
    <style>
        * { box-sizing: border-box; }
        body, html { margin: 0; padding: 0; }

        .auth-main-row {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .auth-form-col {
            width: 45%;    
            background: #f4f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .blob-tl {
            top: -60px; left: -60px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(121, 40, 202, 0.12), transparent 90%);
        }

        .blob-tr {
            top: -50px; right: -50px;
            width: 180px; height: 180px;
            background: radial-gradient(circle, rgba(100, 180, 255, 0.15), transparent 90%);
        }

        .blob-bl {
            bottom: -50px; left: -50px;
            width: 160px; height: 160px;
            background: radial-gradient(circle, rgba(203, 12, 159, 0.10), transparent 90%);
        }

        .blob-br {
            bottom: -60px; right: -60px;
            width: 220px; height: 220px;
            background: radial-gradient(circle, rgba(203, 12, 159, 0.10), transparent 90%);
        }

        .auth-form-box {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 1;
            background: #fff;
            border-radius: 20px;
            padding: 52px 48px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.10);
            min-height: 520px;
        }

        .auth-form-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
            text-align: center;
        }

        .auth-form-subtitle {
            color: #999;
            font-size: 0.92rem;
            margin-bottom: 32px;
            text-align: center;
        }

        .auth-form-box .text-center.mb-4 { 
            display: block !important; 
            margin-bottom: 20px !important;
        }

        .auth-form-box .text-center.mb-4 img {
            max-height: 50px !important;
            width: auto !important;
        }

        .auth-form-box .form-control,
        .auth-form-box input[type="text"],
        .auth-form-box input[type="email"],
        .auth-form-box input[type="password"] {
            background-color: #f0f4ff !important;
            border: 1.5px solid #e8eaf6 !important;
            border-radius: 10px !important;
            padding: 13px 16px !important;
            font-size: 0.95rem !important;
            color: #333 !important;
            transition: all 0.2s;
            box-shadow: none !important;
        }

        .auth-form-box .form-control:focus,
        .auth-form-box input:focus {
            background-color: #fff !important;
            border-color: #c0392b !important;
            box-shadow: 0 6px 18px rgba(200, 30, 0, 0.45) !important;
            outline: none !important;
        }

        .auth-form-box label {
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            color: #555 !important;
            margin-bottom: 6px !important;
        }

        .auth-form-box .form-group,
        .auth-form-box .mb-3 {
            margin-bottom: 1.1rem !important;
        }

        .auth-form-box .input-group {
            width: 100% !important;
            display: flex !important;
            flex-wrap: nowrap !important;
        }

        .auth-form-box .input-group .form-control {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            border-right: none !important;
            border-radius: 10px 0 0 10px !important;
        }

        .auth-form-box .input-group-text {
            flex-shrink: 0 !important;
            background: #f0f4ff !important;
            border: 1.5px solid #e8eaf6 !important;
            border-left: none !important;
            border-radius: 0 10px 10px 0 !important;
            cursor: pointer;
            color: #aaa;
        }
 
        .auth-form-box button[type="submit"],
        .auth-form-box input[type="submit"],
        .auth-form-box .btn-primary {
            background-color: #c0392b !important;
            border-color: #c0392b !important;
            color: #fff !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            font-size: 0.88rem !important;
            letter-spacing: 1.5px !important;
            text-transform: uppercase !important;
            padding: 14px !important;
            width: 100% !important;
            box-shadow: 0 4px 20px rgba(200, 30, 0, 0.35) !important;
            transition: all 0.25s ease !important;
            border: none !important;
            cursor: pointer;
        }

        .auth-form-box button[type="submit"]:hover,
        .auth-form-box .btn-primary:hover {
            background-color: #a93226 !important;
            box-shadow: 0 6px 28px rgba(200, 30, 0, 0.45) !important;
            transform: translateY(-1px);
        }

        .auth-form-box a {
            color: #c0392b !important;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-form-box a:hover { color: #c0392b !important; text-decoration: underline; }

        .auth-form-box .form-check-input:checked {
            background-color: #c0392b !important;
            border-color: #c0392b !important;
        }
        .auth-form-box .form-check-label { font-size: 0.88rem; color: #666; }

        .auth-form-box .alert { border-radius: 10px !important; font-size: 0.88rem; margin-bottom: 1rem; }

        .auth-image-col {
            width: 55%;
            position: relative;
            overflow: hidden;
        }

        .auth-image-col .bg-cover {
            background-image: url({{ $backgroundUrl }});
            background-size: cover;
            background-position: center;
            width: 100%;
            height: 100%;
            min-height: 100vh;
        }

        .auth-image-caption {
            position: absolute;
            bottom: 0;
            right: 0;
            padding: 24px 32px;
            color: #fff;
            text-shadow: 0 1px 4px rgba(0,0,0,0.5);
        }

        .auth-image-caption h1 { font-size: 1.4rem; font-weight: 700; margin-bottom: 4px; }
        .auth-image-caption p { font-size: 0.82rem; opacity: 0.85; margin: 0; }

        @media (max-width: 991px) {
            .auth-form-col { width: 50%; padding: 40px 32px; }
            .auth-image-col { width: 50%; }
        }

        @media (max-width: 768px) {
            .auth-main-row { flex-direction: column; }
            .auth-form-col { width: 100%; padding: 40px 24px; }
            .auth-image-col { display: none !important; }
        }
    </style>

    <div class="auth-main-row">
        <div class="auth-form-col">
            <span class="blob blob-tl"></span>
            <span class="blob blob-tr"></span>
            <span class="blob blob-bl"></span>
            <span class="blob blob-br"></span>

            <div class="auth-form-box">
                <div class="text-center mb-4">
                    @include('core/base::partials.logo', ['defaultLogoHeight' => 50])
                </div>

                <h2 class="auth-form-title">Admin Sign In</h2>
                <p class="auth-form-subtitle">Sign in to stay connected</p>
                @yield('content')
            </div>
        </div>

        <div class="auth-image-col d-none d-lg-block">
            <div class="bg-cover h-100 min-vh-100"></div>
            <div class="auth-image-caption">
                <h1>{{ setting('admin_title', config('core.base.general.base_name')) }}</h1>
                <p>@include('core/base::partials.copyright')</p>
            </div>
        </div>
    </div>
</x-core::layouts.base>