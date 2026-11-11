<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Acceso') - {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-primary: #1B1B18;
            --brand-secondary: #374fa7;
            --brand-muted: #706f6c;
            --brand-light: #FDFDFC;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #FDFDFC 0%, #f3f4f6 100%);
            color: var(--brand-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .auth-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            border: 1px solid #e5e7eb;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-brand img {
            max-height: 60px;
            margin-bottom: 0.75rem;
        }

        .auth-brand h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .auth-brand h1 span { color: var(--brand-secondary); }

        .auth-brand p {
            color: var(--brand-muted);
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--brand-primary);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: white;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc2626;
        }

        .invalid-feedback {
            color: #ff0000;
            font-size: 0.85rem;
            margin-top: 0.35rem;
        }

        .btn-auth {
            width: 100%;
            padding: 0.75rem;
            background: var(--brand-secondary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .btn-auth:hover {
            background: #000000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(55, 79, 167, 0.3);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--brand-muted);
        }

        .auth-footer a {
            color: var(--brand-secondary);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer a:hover { text-decoration: underline; }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-row label {
            margin: 0;
            font-size: 0.9rem;
            cursor: pointer;
            color: var(--brand-primary);
        }

        .phone-field .iti {
            width: 100%;
        }

        .phone-field .iti__tel-input {
            width: 100%;
            padding: 0.7rem 0.875rem;
            font-size: 0.95rem;
            border: 1px solid #d1d5db;
            border-radius: 0 0.5rem 0.5rem 0;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .phone-field .iti__tel-input:focus {
            outline: none;
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.1);
        }

        .phone-field .iti--separate-dial-code .iti__selected-dial-code {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--brand-primary);
        }

        .phone-field .iti__selected-country {
            border-radius: 0.5rem 0 0 0.5rem;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-right: none;
            padding: 0 0.5rem;
        }

        .phone-field .iti__selected-country:hover {
            background: #f3f4f6;
        }

        .phone-field .iti__arrow {
            border-top-color: var(--brand-muted);
        }

        .phone-field .iti__country-list {
            max-height: 280px;
            font-size: 0.9rem;
            z-index: 100;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="auth-card">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>