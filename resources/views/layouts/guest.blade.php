<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ingest') }}</title>

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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Guest Custom Style Overrides -->
        <style>
            body {
                background-color: #0b0f19 !important;
                background-image: 
                    radial-gradient(at 10% 20%, rgba(99, 102, 241, 0.12) 0px, transparent 50%),
                    radial-gradient(at 90% 80%, rgba(217, 119, 6, 0.08) 0px, transparent 50%) !important;
                font-family: 'Outfit', sans-serif !important;
                color: #f8fafc !important;
            }
            .glass-container {
                background: rgba(21, 29, 48, 0.75) !important;
                backdrop-filter: blur(12px) !important;
                -webkit-backdrop-filter: blur(12px) !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                box-shadow: 0 10px 40px 0 rgba(0, 0, 0, 0.4) !important;
                border-radius: 16px !important;
                padding: 2.5rem !important;
            }
            /* Form inputs override */
            .glass-container input:not([type="checkbox"]) {
                background-color: rgba(255, 255, 255, 0.04) !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                color: #f8fafc !important;
                border-radius: 10px !important;
                padding: 12px 16px !important;
                height: 48px !important;
                transition: all 0.2s ease !important;
                width: 100% !important;
            }
            .glass-container input:not([type="checkbox"]):focus {
                border-color: #6366f1 !important;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
                background-color: rgba(255, 255, 255, 0.06) !important;
                outline: none !important;
            }
            /* Chrome/Safari autofill override */
            .glass-container input:not([type="checkbox"]):-webkit-autofill,
            .glass-container input:not([type="checkbox"]):-webkit-autofill:hover, 
            .glass-container input:not([type="checkbox"]):-webkit-autofill:focus, 
            .glass-container input:not([type="checkbox"]):-webkit-autofill:active {
                -webkit-background-clip: text;
                -webkit-text-fill-color: #f8fafc !important;
                transition: background-color 5000s ease-in-out 0s;
                box-shadow: inset 0 0 20px 20px rgba(21, 29, 48, 0.95) !important;
            }
            /* Form labels override */
            label, .text-gray-600 {
                color: #94a3b8 !important;
                font-weight: 500 !important;
                margin-bottom: 0.5rem !important;
                display: block !important;
            }
            /* Form layout spacing */
            .glass-container form > div {
                margin-bottom: 1.5rem !important;
            }
            .glass-container form > div:last-child {
                margin-bottom: 0 !important;
            }
            /* Form links override */
            a.text-gray-600 {
                color: #94a3b8 !important;
                transition: color 0.2s ease !important;
                text-decoration: none !important;
            }
            a.text-gray-600:hover {
                color: #f8fafc !important;
            }
            /* Primary Button override */
            button, .btn-primary, [type="submit"] {
                background-color: #6366f1 !important;
                border: none !important;
                border-radius: 10px !important;
                font-weight: 600 !important;
                color: #ffffff !important;
                box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.4) !important;
                transition: all 0.2s ease !important;
                padding: 0.75rem 1.75rem !important;
                cursor: pointer !important;
            }
            button:hover, .btn-primary:hover, [type="submit"]:hover {
                background-color: #4f46e5 !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 6px 20px 0 rgba(99, 102, 241, 0.6) !important;
            }
            /* Checkbox style override */
            input[type="checkbox"] {
                background-color: rgba(255, 255, 255, 0.04) !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                border-radius: 4px !important;
                color: #6366f1 !important;
            }
            input[type="checkbox"]:focus {
                ring-color: #6366f1 !important;
            }

            /* Light Mode CSS Overrides */
            [data-bs-theme="light"] body {
                background-color: #f8fafc !important;
                background-image: 
                    radial-gradient(at 10% 20%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                    radial-gradient(at 90% 80%, rgba(217, 119, 6, 0.03) 0px, transparent 50%) !important;
                color: #0f172a !important;
            }
            [data-bs-theme="light"] .glass-container {
                background: rgba(255, 255, 255, 0.85) !important;
                border-color: rgba(0, 0, 0, 0.08) !important;
                box-shadow: 0 10px 40px 0 rgba(0, 0, 0, 0.08) !important;
                padding: 2.5rem !important;
            }
            [data-bs-theme="light"] .glass-container input:not([type="checkbox"]) {
                background-color: rgba(0, 0, 0, 0.02) !important;
                border-color: rgba(0, 0, 0, 0.08) !important;
                color: #0f172a !important;
                padding: 12px 16px !important;
                height: 48px !important;
            }
            [data-bs-theme="light"] .glass-container input:not([type="checkbox"]):focus {
                border-color: #6366f1 !important;
                background-color: rgba(0, 0, 0, 0.04) !important;
            }
            [data-bs-theme="light"] label, [data-bs-theme="light"] .text-gray-600 {
                color: #475569 !important;
            }
            [data-bs-theme="light"] a.text-gray-600 {
                color: #475569 !important;
            }
            [data-bs-theme="light"] a.text-gray-600:hover {
                color: #0f172a !important;
            }
            [data-bs-theme="light"] .glass-container input:not([type="checkbox"]):-webkit-autofill,
            [data-bs-theme="light"] .glass-container input:not([type="checkbox"]):-webkit-autofill:hover,
            [data-bs-theme="light"] .glass-container input:not([type="checkbox"]):-webkit-autofill:focus,
            [data-bs-theme="light"] .glass-container input:not([type="checkbox"]):-webkit-autofill:active {
                -webkit-text-fill-color: #0f172a !important;
                box-shadow: inset 0 0 20px 20px rgba(255, 255, 255, 0.95) !important;
                -webkit-background-clip: text !important;
            }
            [data-bs-theme="light"] input[type="checkbox"] {
                background-color: rgba(0, 0, 0, 0.02) !important;
                border-color: rgba(0, 0, 0, 0.08) !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-4">
                <a href="/">
                    <x-application-logo class="w-24 h-24" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-4 glass-container overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
