<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=syne:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Syne', sans-serif;
            }

            .hero-shell {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background:
                    radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08) 0 1px, transparent 1px 100%),
                    radial-gradient(circle at 80% 30%, rgba(255,255,255,0.08) 0 1px, transparent 1px 100%),
                    radial-gradient(circle at 35% 75%, rgba(255,255,255,0.08) 0 1px, transparent 1px 100%),
                    linear-gradient(180deg, #0f5b3a 0%, #0d4f33 100%);
                background-size: 28px 28px, 28px 28px, 28px 28px, 100% 100%;
                overflow: hidden;
                position: relative;
                color: #fff;
            }

            .hero-shell::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(circle at 50% 50%, rgba(255,255,255,0.08), transparent 42%),
                    radial-gradient(circle at 15% 80%, rgba(246,173,60,0.16), transparent 22%),
                    radial-gradient(circle at 85% 18%, rgba(255,255,255,0.07), transparent 18%);
                pointer-events: none;
            }

            .hero-content {
                position: relative;
                z-index: 1;
                text-align: center;
                padding: 2rem 1.25rem;
                max-width: 1100px;
            }

            .hero-title {
                margin: 0;
                font-size: clamp(2.25rem, 5.2vw, 4.75rem);
                line-height: 0.95;
                letter-spacing: -0.055em;
                font-weight: 800;
                text-transform: none;
                color: #ffffff;
                text-shadow: 0 6px 30px rgba(0, 0, 0, 0.18);
                max-width: 12ch;
            }

            .hero-subtitle {
                margin-top: 1.25rem;
                font-size: clamp(1rem, 1.8vw, 1.25rem);
                color: rgba(255,255,255,0.88);
                font-weight: 500;
                letter-spacing: 0.01em;
            }

            .hero-actions {
                margin-top: 2rem;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.85rem;
            }

            .hero-btn {
                border-radius: 999px;
                padding: 0.9rem 1.45rem;
                font-weight: 700;
                text-decoration: none;
                transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            }

            .hero-btn:hover {
                transform: translateY(-2px);
            }

            .hero-btn-primary {
                background: #f6ad3c;
                color: #0f172a;
                box-shadow: 0 10px 24px rgba(246,173,60,0.28);
            }

            .hero-btn-secondary {
                border: 1px solid rgba(255,255,255,0.28);
                color: #fff;
                background: rgba(255,255,255,0.08);
                backdrop-filter: blur(8px);
            }
        </style>
    </head>
    <body class="font-sans antialiased" style="font-family: 'Syne', sans-serif;">
        <div class="hero-shell">
            <div class="hero-content">
                <h1 class="hero-title">Shop Safe, Shop Smart with Smarttrade Africa <span aria-hidden="true">🌍</span></h1>
                <p class="hero-subtitle">Smarttrade Africa brings you a simple, secure, and modern shopping experience.</p>
                <div class="hero-actions">
                    <a href="{{ route('products.index') }}" class="hero-btn hero-btn-primary">Browse Products</a>
                </div>
            </div>
        </div>
    </body>
</html>
