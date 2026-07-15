<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingest Mobile Setup</title>
    
    <!-- Google Fonts: Outfit -->
    <link href="{{ asset('assets/css/outfit.css') }}" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="{{ asset('assets/css/bootstrap-icons.css') }}" rel="stylesheet">

    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(21, 29, 48, 0.7);
            --card-border: rgba(255, 255, 255, 0.08);
            --primary-accent: #6366f1;
            --primary-accent-glow: rgba(99, 102, 241, 0.15);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 10% 20%, rgba(99, 102, 241, 0.1) 0px, transparent 50%);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 2.5rem;
        }

        .form-control-custom {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            color: var(--text-main) !important;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 3px var(--primary-accent-glow);
            outline: none;
        }

        .form-label-custom {
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .btn-primary-custom {
            background-color: var(--primary-accent);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            color: #fff;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.4);
        }

        .btn-primary-custom:hover {
            background-color: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px 0 rgba(99, 102, 241, 0.6);
            color: #fff;
        }
        
        .code-input-group {
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .code-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <div class="glass-card">
        <div class="text-center mb-4">
            <a href="#" class="d-inline-flex align-items-center gap-2 mb-3 text-decoration-none" style="font-weight: 800; font-size: 2rem; color: var(--text-main);">
                <i class="bi bi-heart-pulse-fill text-indigo" style="color: var(--primary-accent);"></i>
                <span>INGEST MOBILE</span>
            </a>
            <h4 class="fw-bold">Initial Mobile Setup</h4>
            <p class="text-muted small">Connect your offline mobile app to your web account to enable secure access and background sync.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 p-3 mb-4" role="alert">
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mobile-setup.submit') }}" method="POST" id="setupForm">
            @csrf

            <!-- Setup Code Input -->
            <div class="mb-4">
                <label for="code" class="form-label-custom">Registration Code</label>
                <input type="text" name="code" id="code" class="form-control form-control-custom text-center fw-bold fs-3 text-uppercase" placeholder="000000" maxlength="6" value="{{ old('code') }}" required autocomplete="off">
                <div class="form-text text-muted small text-center mt-1">Get this code from the "Offline Setup" page on the web app.</div>
            </div>

            <!-- Server URL Input -->
            <div class="mb-4">
                <label for="server_url" class="form-label-custom">Web App Server URL</label>
                <input type="url" name="server_url" id="server_url" class="form-control form-control-custom" value="{{ old('server_url', 'https://ingest.wabwi.com') }}" required placeholder="https://ingest.wabwi.com">
                <div class="form-text text-muted small mt-1">Keep the default unless testing on a local development server.</div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 mt-2">
                <i class="bi bi-link-45deg me-2"></i> Verify and Complete Setup
            </button>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        document.getElementById('code').addEventListener('input', function (e) {
            // Automatically clean code: digits only
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
