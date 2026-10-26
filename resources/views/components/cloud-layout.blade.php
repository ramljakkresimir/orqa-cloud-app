@props(['title' => 'Dashboard'])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | ORQA Cloud</title>
    @vite(['resources/css/cloud.css', 'resources/js/cloud.js'])
</head>
<body class="cloud-body">
@php
    $isAdmin = auth()->user()->hasRole('super_admin');
    $dashboardRoute = $isAdmin ? 'admin.dashboard' : 'user.dashboard';
@endphp
<div class="cloud-shell">
    <aside class="cloud-sidebar d-none d-lg-flex flex-column" aria-label="Main navigation">
        <a href="{{ route('dashboard') }}" class="cloud-brand">
            <span class="cloud-brand-mark"><i class="bi bi-cloud-fill" aria-hidden="true"></i></span>
            <span>ORQA <strong>Cloud</strong></span>
        </a>
        @include('components.cloud-navigation')
    </aside>

    <div class="cloud-content">
        <header class="cloud-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border d-lg-none" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#cloudMobileMenu" aria-controls="cloudMobileMenu" aria-label="Open menu">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <span class="cloud-page-label">{{ $title }}</span>
            </div>
            <div class="cloud-account"><span class="cloud-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
            </div>
        </header>
        <main class="cloud-main">
            @if (session('success'))
                <div class="alert alert-success" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
<div class="offcanvas offcanvas-start" tabindex="-1" id="cloudMobileMenu" aria-labelledby="cloudMobileMenuLabel">
    <div class="offcanvas-header border-bottom">
        <h2 class="offcanvas-title fs-5" id="cloudMobileMenuLabel">ORQA Cloud</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        @include('components.cloud-navigation')
    </div>
</div>
</body>
</html>
