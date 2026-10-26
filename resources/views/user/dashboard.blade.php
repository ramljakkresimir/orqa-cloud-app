<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Dashboard - ORQA Cloud</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1>My Dashboard</h1>

        <a href="{{ route('files.create') }}"
        class="btn btn-primary mt-3">
            Upload New File
        </a>

        <a href="{{ route('files.index') }}"
        class="btn btn-outline-primary mt-3">
            My Files
        </a>

        <a href="{{ route('profile.edit') }}"
        class="btn btn-outline-secondary mt-3">
            My Profile
        </a>

        <p>Welcome, {{ auth()->user()->name }}!</p>

        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif
        
        @php
            $user = auth()->user();

            $totalBytes = (int) $user->quota_bytes;
            $usedBytes = $user->usedStorageBytes();

            $remainingBytes = max(0, $totalBytes - $usedBytes);

            $usedPercentage = $totalBytes > 0
                ? min(100, round(($usedBytes / $totalBytes) * 100, 1))
                : 0;

            $toGB = fn (int $bytes): string =>
                number_format($bytes / (1024 ** 3), 2);
        @endphp

        <div class="card mt-4">
            <div class="card-body">

                <h5 class="card-title mb-4">My Storage</h5>

                <div class="row text-center mb-4">
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Total Space</p>
                        <h4>{{ $toGB($totalBytes) }} GB</h4>
                    </div>

                    <div class="col-md-4">
                        <p class="text-muted mb-1">Used Space</p>
                        <h4>{{ $toGB($usedBytes) }} GB</h4>
                    </div>

                    <div class="col-md-4">
                        <p class="text-muted mb-1">Free Space</p>
                        <h4 class="text-success">
                            {{ $toGB($remainingBytes) }} GB
                        </h4>
                    </div>
                </div>

                <div
                    class="progress"
                    role="progressbar"
                    aria-label="Storage usage"
                    aria-valuenow="{{ $usedPercentage }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="height: 18px;"
                >
                    <div
                        class="progress-bar bg-success"
                        style="width: {{ $usedPercentage }}%;"
                    >
                        {{ $usedPercentage >= 10 ? $usedPercentage.'%' : '' }}
                    </div>
                </div>

                <p class="text-muted mt-2 mb-0">
                    {{ $usedPercentage }}% of storage used
                </p>

            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}"
              class="mt-4">
            @csrf

            <button type="submit"
                    class="btn btn-outline-danger">
                Logout
            </button>
        </form>
    </div>
</body>
</html>