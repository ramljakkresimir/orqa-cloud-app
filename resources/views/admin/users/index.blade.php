<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>User Storage - ORQA Cloud</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">
    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>User Storage</h1>

            <a
                href="{{ route('admin.dashboard') }}"
                class="btn btn-outline-secondary"
            >
                Back to Dashboard
            </a>
        </div>

        <div class="card">
            <div class="card-body">

                @php
                    $toGB = fn (int $bytes): string =>
                        number_format($bytes / (1024 ** 3), 2);
                @endphp

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Files</th>
                                <th>Total Space</th>
                                <th>Used Space</th>
                                <th>Free Space</th>
                                <th>Usage</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                @php
                                    $totalBytes = (int) $user->quota_bytes;
                                    $usedBytes = (int) ($user->used_bytes ?? 0);

                                    $remainingBytes = max(
                                        0,
                                        $totalBytes - $usedBytes
                                    );

                                    $usedPercentage = $totalBytes > 0
                                        ? min(
                                            100,
                                            round(($usedBytes / $totalBytes) * 100, 1)
                                        )
                                        : 0;
                                @endphp

                                <tr>
                                    <td>{{ $user->username }}</td>

                                    <td>
                                        {{ trim($user->name.' '.($user->surname ?? '')) }}
                                    </td>

                                    <td>
                                        <a href="{{ route('files.index') }}">
                                            View All Files
                                        </a>
                                    </td>

                                    <td>{{ $toGB($totalBytes) }} GB</td>

                                    <td>{{ $toGB($usedBytes) }} GB</td>

                                    <td>
                                        <span class="text-success fw-semibold">
                                            {{ $toGB($remainingBytes) }} GB
                                        </span>
                                    </td>

                                    <td style="min-width: 130px;">
                                        <div
                                            class="progress"
                                            role="progressbar"
                                            aria-label="Storage usage for {{ $user->username }}"
                                            aria-valuenow="{{ $usedPercentage }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        >
                                            <div
                                                class="progress-bar bg-success"
                                                style="width: {{ $usedPercentage }}%"
                                            ></div>
                                        </div>

                                        <small>{{ $usedPercentage }}%</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>

    </div>
</body>
</html>