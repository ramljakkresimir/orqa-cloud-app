<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>My Files - ORQA Cloud</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">
    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                {{ $isSuperAdmin ? 'All Files' : 'My Files' }}
            </h1>

            <a
                href="{{ route(
                    $isSuperAdmin ? 'admin.dashboard' : 'user.dashboard'
                ) }}"
                class="btn btn-outline-secondary"
            >
                Back to Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (! $isSuperAdmin)
            <div class="mb-4">
                <a href="{{ route('files.create') }}"
                   class="btn btn-primary">
                    Upload New File
                </a>
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">

                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Uploaded At</th>

                                @if ($isSuperAdmin)
                                    <th>Added By</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($files as $file)
                                <tr>
                                    <td>
                                        {{ $file->original_name }}
                                    </td>

                                    <td>
                                        {{ strtoupper($file->extension ?: 'FILE') }}
                                    </td>

                                    <td>
                                        {{ number_format(
                                            $file->size_bytes / 1024 / 1024,
                                            2
                                        ) }} MiB
                                    </td>

                                    <td>
                                        {{ $file->created_at->format('d.m.Y. H:i') }}
                                    </td>

                                    @if ($isSuperAdmin)
                                        <td>
                                            {{ $file->user?->username ?? 'Unknown' }}
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="{{ $isSuperAdmin ? 5 : 4 }}"
                                        class="text-center text-muted py-4"
                                    >
                                        No files found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="mt-4">
                    {{ $files->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>

    </div>
</body>
</html>