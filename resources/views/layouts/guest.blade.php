<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans text-gray-900">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-brand-primary py-4">
        <div class="mb-4">
            <a href="/" class="text-decoration-none">
                <h1 class="fw-bold text-white display-4"><span class="text-brand-secondary">Now</span>IA</h1>
            </a>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5 bg-dark border border-secondary rounded text-light">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>