<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ingest - Track Your Food & Digestive Health</title>

    <!-- Theme Initializer -->
    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Premium Custom Styles -->
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(21, 29, 48, 0.7);
            --card-border: rgba(255, 255, 255, 0.08);
            --primary-accent: #6366f1;
            --primary-accent-glow: rgba(99, 102, 241, 0.15);
            --poop-accent: #d97706;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 10% 20%, rgba(99, 102, 241, 0.12) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(217, 119, 6, 0.08) 0px, transparent 50%);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .btn-primary-custom {
            background-color: var(--primary-accent);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 0.8rem 2rem;
            color: #fff;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.4);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-custom:hover {
            background-color: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px 0 rgba(99, 102, 241, 0.6);
            color: #fff;
        }

        .btn-outline-custom {
            background-color: transparent;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            font-weight: 600;
            padding: 0.8rem 2rem;
            color: var(--text-main);
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-custom:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: var(--text-muted);
            transform: translateY(-2px);
            color: #fff;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-accent);
            margin-bottom: 1.5rem;
            display: inline-block;
            background: var(--primary-accent-glow);
            padding: 0.5rem 1rem;
            border-radius: 12px;
        }

        .poop-icon {
            color: var(--poop-accent);
            background: rgba(217, 119, 6, 0.15);
        }

        .reminder-icon {
            color: #10b981;
            background: rgba(16, 185, 129, 0.15);
        }

        .feature-card {
            border: 1px solid var(--card-border);
            background: rgba(255, 255, 255, 0.02);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.04);
        }

        /* Light Mode Custom Styling Overrides */
        [data-bs-theme="light"] {
            --bg-color: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(0, 0, 0, 0.08);
            --primary-accent: #4f46e5;
            --primary-accent-glow: rgba(79, 70, 229, 0.08);
            --poop-accent: #d97706;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        [data-bs-theme="light"] body {
            background-image: 
                radial-gradient(at 10% 20%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(217, 119, 6, 0.03) 0px, transparent 50%) !important;
        }

        [data-bs-theme="light"] .feature-card {
            background: rgba(0, 0, 0, 0.01) !important;
            border-color: rgba(0, 0, 0, 0.06) !important;
        }

        [data-bs-theme="light"] .feature-card:hover {
            background: rgba(0, 0, 0, 0.02) !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
        }

        [data-bs-theme="light"] .btn-outline-custom {
            color: #475569 !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
        }
        [data-bs-theme="light"] .btn-outline-custom:hover {
            background: rgba(0, 0, 0, 0.04) !important;
            color: #0f172a !important;
        }

        [data-bs-theme="light"] header span {
            color: #0f172a !important;
        }
        [data-bs-theme="light"] header i {
            color: var(--primary-accent) !important;
        }

        /* Responsive Brand Casing & Nav Header */
        .navbar-brand-custom {
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            color: var(--text-main);
            text-decoration: none;
        }

        /* Mobile View UI adjustments for Header Buttons */
        @media (max-width: 576px) {
            .navbar-brand-custom {
                font-size: 1.25rem !important;
            }
            header .btn-primary-custom, header .btn-outline-custom {
                padding: 0.4rem 0.75rem !important;
                font-size: 0.85rem !important;
                white-space: nowrap !important;
            }
            .header-actions {
                gap: 0.5rem !important;
            }
            .auth-buttons {
                gap: 0.35rem !important;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-h-screen justify-content-between">

    <!-- Header Navigation -->
    <header class="py-4">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="navbar-brand-custom d-flex align-items-center gap-2">
                <i class="bi bi-heart-pulse-fill" style="color: var(--primary-accent);"></i>
                <span>INGEST</span>
            </a>
            <div class="header-actions d-flex align-items-center gap-3">
                <!-- Theme Toggle Button -->
                <button type="button" id="themeToggle" class="btn btn-sm btn-outline-custom border-0 py-2 px-3" title="Toggle Light/Dark Theme">
                    <i class="bi bi-sun-fill" id="theme-icon"></i>
                </button>

                @if (Route::has('login'))
                    <div class="auth-buttons d-flex gap-2">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary-custom py-2 px-4" style="border-radius: 10px;">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-outline-custom py-2 px-4 border-0" style="border-radius: 10px;">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary-custom py-2 px-4" style="border-radius: 10px;">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Hero Area -->
    <main class="my-auto py-5">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <span class="badge mb-3 py-2 px-3 text-uppercase" style="background: var(--primary-accent-glow); color: var(--primary-accent); font-weight: 700; border: 1px solid rgba(99, 102, 241, 0.2);">
                        <i class="bi bi-shield-heart me-1"></i> Personal Health Companion
                    </span>
                    <h1 class="display-3 fw-bold mb-3" style="letter-spacing: -1.5px; background: linear-gradient(to right, #f8fafc, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        Track What You Eat & When You Poop
                    </h1>
                    <p class="lead text-muted mb-5 mx-auto" style="max-width: 600px; font-size: 1.25rem;">
                        Understand your body's digestive rhythms. Easily log meals, track bowel movements using the Bristol Stool Chart, and receive smart reminders to keep logging.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary-custom">
                                <i class="bi bi-grid-fill me-2"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-primary-custom">
                                <i class="bi bi-rocket-takeoff-fill me-2"></i> Get Started Free
                            </a>
                            <a href="{{ route('login') }}" class="btn-outline-custom">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="row g-4 mt-5">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Food Consistency</h4>
                        <p class="text-muted mb-0" style="font-size: 0.95rem;">
                            Log breakfast, lunch, dinner, or snacks. Identify patterns of when and how well you are eating throughout the day.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon poop-icon">
                            <i class="bi bi-activity"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Stool Regularity</h4>
                        <p class="text-muted mb-0" style="font-size: 0.95rem;">
                            Record bowel movements using the standard Bristol Stool Chart to monitor consistency and identify digestive changes.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon reminder-icon">
                            <i class="bi bi-envelope-check"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Smart Reminders</h4>
                        <p class="text-muted mb-0" style="font-size: 0.95rem;">
                            Set up automated mid-day email alerts so you never forget to log. Stay accountable and consistent.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-4 border-top" style="border-color: var(--card-border) !important; background: rgba(11, 15, 25, 0.4);">
        <div class="container text-center text-muted">
            <p class="mb-0" style="font-size: 0.9rem;">&copy; {{ date('Y') }} Ingest Tracker. Take control of your daily habits.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Toggle Handler Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function () {
                    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateThemeButton(newTheme);
                });
            }

            function updateThemeButton(theme) {
                const icon = document.getElementById('theme-icon');
                if (icon) {
                    if (theme === 'light') {
                        icon.className = 'bi bi-moon-fill';
                    } else {
                        icon.className = 'bi bi-sun-fill';
                    }
                }
            }

            // Initialize button icon on load
            updateThemeButton(document.documentElement.getAttribute('data-bs-theme'));
        });
    </script>
</body>
</html>
