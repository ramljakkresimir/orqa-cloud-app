<x-cloud-layout title="Admin Dashboard">
    @php
        $formatBytes = fn (int $bytes): string => $bytes >= 1024 ** 3
            ? number_format($bytes / (1024 ** 3), 2) . ' GB'
            : number_format($bytes / (1024 ** 2), 2) . ' MB';
    @endphp

    <div class="cloud-dashboard-heading d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><p class="cloud-muted text-uppercase small fw-semibold mb-2">Admin workspace</p><h1 class="h2 fw-bold mb-2">Welcome, {{ auth()->user()->name }}!</h1><p class="cloud-muted mb-0">Overview of your cloud files and users.</p></div>
        <a href="{{ route('files.index') }}" class="btn btn-primary cloud-action-btn"><i class="bi bi-folder2-open me-2" aria-hidden="true"></i>All files</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4"><div class="card cloud-card cloud-stat-card h-100"><div class="card-body"><div class="cloud-stat-icon"><i class="bi bi-files" aria-hidden="true"></i></div><p class="cloud-muted mb-1">Total files</p><div class="cloud-stat-value">{{ number_format($filesCount) }}</div></div></div></div>
        <div class="col-12 col-md-4"><div class="card cloud-card cloud-stat-card h-100"><div class="card-body"><div class="cloud-stat-icon"><i class="bi bi-people" aria-hidden="true"></i></div><p class="cloud-muted mb-1">Users</p><div class="cloud-stat-value">{{ number_format($usersCount) }}</div></div></div></div>
        <div class="col-12 col-md-4"><div class="card cloud-card cloud-stat-card h-100"><div class="card-body"><div class="cloud-stat-icon"><i class="bi bi-device-hdd" aria-hidden="true"></i></div><p class="cloud-muted mb-1">Total stored</p><div class="cloud-stat-value">{{ $formatBytes($usedBytes) }}</div></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6"><section class="card cloud-card h-100"><div class="card-body"><div class="cloud-stat-icon"><i class="bi bi-folder2-open" aria-hidden="true"></i></div><h2 class="h5 fw-semibold">File management</h2><p class="cloud-muted">Browse, download and manage files uploaded by all users.</p><a class="btn btn-outline-primary" href="{{ route('files.index') }}">View all files</a></div></section></div>
        <div class="col-12 col-md-6"><section class="card cloud-card h-100"><div class="card-body"><div class="cloud-stat-icon"><i class="bi bi-people" aria-hidden="true"></i></div><h2 class="h5 fw-semibold">User storage</h2><p class="cloud-muted">Review registered users and their remaining storage quota.</p><a class="btn btn-outline-primary" href="{{ route('admin.users.index') }}">View users</a></div></section></div>
    </div>

    <section class="card cloud-card"><div class="card-body">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><h2 class="h5 fw-semibold mb-1">Recent uploads</h2><p class="cloud-muted small mb-0">Latest files uploaded across the cloud</p></div><a href="{{ route('files.index') }}" class="btn btn-outline-primary btn-sm">View all</a></div>
        @if ($recentFiles->isEmpty())
            <div class="cloud-empty-state text-center"><i class="bi bi-folder2-open" aria-hidden="true"></i><p class="fw-semibold mt-2 mb-1">No files yet</p><p class="cloud-muted small mb-0">Uploaded documents will appear here.</p></div>
        @else
            <div class="table-responsive"><table class="table table-hover cloud-dashboard-table align-middle mb-0"><thead><tr><th scope="col">File</th><th scope="col">Added by</th><th scope="col">Size</th><th scope="col">Uploaded</th><th scope="col"><span class="visually-hidden">Action</span></th></tr></thead><tbody>
            @foreach ($recentFiles as $file)
                <tr><td><i class="bi bi-file-earmark text-primary me-2" aria-hidden="true"></i><span class="cloud-file-name">{{ $file->original_name }}</span></td><td>{{ $file->user?->username ?? 'Unknown' }}</td><td class="text-nowrap">{{ $formatBytes((int) $file->size_bytes) }}</td><td class="text-nowrap">{{ $file->created_at->format('d M Y') }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('files.download', $file) }}">Download</a></td></tr>
            @endforeach
            </tbody></table></div>
        @endif
    </div></section>
</x-cloud-layout>
