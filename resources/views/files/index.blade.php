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
    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
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
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($files as $file)
                                @php
                                    $extension = strtolower($file->extension ?? '');

                                    $iconClass = match ($extension) {
                                        'pdf' => 'bi-file-earmark-pdf-fill text-danger',

                                        'doc', 'docx' =>
                                            'bi-file-earmark-word-fill text-primary',

                                        'xls', 'xlsx', 'csv' =>
                                            'bi-file-earmark-excel-fill text-success',

                                        'ppt', 'pptx' =>
                                            'bi-file-earmark-ppt-fill text-warning',

                                        'jpg', 'jpeg', 'png', 'gif', 'webp' =>
                                            'bi-file-earmark-image-fill text-info',

                                        'zip', 'rar', '7z' =>
                                            'bi-file-earmark-zip-fill text-secondary',

                                        'txt' =>
                                            'bi-file-earmark-text-fill text-secondary',

                                        default =>
                                            'bi-file-earmark-fill text-secondary',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i
                                                class="bi {{ $iconClass }} fs-4"
                                                aria-hidden="true"
                                            ></i>

                                            <span class="text-break">
                                                {{ $file->original_name }}
                                            </span>
                                        </div>
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
                                        <td>
                                            <div class="d-flex gap-2">

                                                <!-- Download -->
                                                <a
                                                    href="{{ route('files.download', $file) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    Download
                                                </a>

                                                <!-- Delete -->
                                                <form
                                                    action="{{ route('files.destroy', $file) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this file?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="{{ $isSuperAdmin ? 6 : 5 }}"
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