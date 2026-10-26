@props(['title' => 'Welcome', 'subtitle' => 'Securely access your cloud workspace.'])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | ORQA Cloud</title>
    @vite(['resources/css/cloud.css', 'resources/css/cloud-auth.css', 'resources/js/cloud.js'])
</head>
<body class="cloud-body cloud-auth-body">
<div class="cloud-auth-shell">
    <header class="cloud-auth-header">
        <a class="cloud-auth-brand" href="{{ url('/') }}" aria-label="ORQA Cloud home">
            <span class="cloud-brand-mark"><i class="bi bi-cloud-fill" aria-hidden="true"></i></span>
            <span>ORQA <strong>Cloud</strong></span>
        </a>
        <span class="cloud-auth-header-label d-none d-sm-inline">Your files, your workspace.</span>
    </header>
    <main class="cloud-auth-main">
        <div class="cloud-auth-card">
            <div class="cloud-auth-eyebrow"><i class="bi bi-shield-lock" aria-hidden="true"></i> SECURE CLOUD ACCESS</div>
            <h1 class="cloud-auth-title">{{ $title }}</h1>
            <p class="cloud-auth-subtitle">{{ $subtitle }}</p>

            @if (session('status'))
                <div class="alert alert-success" role="status">{{ session('status') }}</div>
            @endif
            {{ $slot }}
        </div>
    </main>
    <footer class="cloud-auth-footer">&copy; {{ date('Y') }} ORQA Cloud</footer>
</div>
</body>
</html>
