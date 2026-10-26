<x-cloud-layout title="My Dashboard">
    @php
        $formatBytes = fn (int $bytes): string => $bytes >= 1024 ** 3
            ? number_format($bytes / (1024 ** 3), 2) . ' GB'
            : number_format($bytes / (1024 ** 2), 2) . ' MB';
    @endphp

    <div class="cloud-dashboard-heading d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <p class="cloud-muted text-uppercase small fw-semibold mb-2">Personal workspace</p>
            <h1 class="h2 fw-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="cloud-muted mb-0">Your files and storage, all in one place.</p>
        </div>
        <a href="{{ route('files.create') }}" class="btn btn-primary cloud-action-btn"><i class="bi bi-cloud-arrow-up me-2" aria-hidden="true"></i>Upload file</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3"><div class="card cloud-card cloud-stat-card h-100"><div class="card-body"><div class="cloud-stat-icon"><i class="bi bi-folder2-open" aria-hidden="true"></i></div><p class="cloud-muted mb-1">My files</p><div class="cloud-stat-value">{{ number_format($filesCount) }}</div></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card cloud-card cloud-stat-card h-100"><div class="card-body"><div class="cloud-stat-icon"><i class="bi bi-device-hdd" aria-hidden="true"></i></div><p class="cloud-muted mb-1">Total storage</p><div class="cloud-stat-value">{{ $formatBytes($totalBytes) }}</div></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card cloud-card cloud-stat-card h-100"><div class="card-body"><div class="cloud-stat-icon"><i class="bi bi-cloud-check" aria-hidden="true"></i></div><p class="cloud-muted mb-1">Used storage</p><div class="cloud-stat-value">{{ $formatBytes($usedBytes) }}</div></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card cloud-card cloud-stat-card h-100"><div class="card-body"><div class="cloud-stat-icon"><i class="bi bi-check-circle" aria-hidden="true"></i></div><p class="cloud-muted mb-1">Available storage</p><div class="cloud-stat-value">{{ $formatBytes($remainingBytes) }}</div></div></div></div>
    </div>

    <section class="card cloud-card mb-4" aria-label="Storage usage"><div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center mb-3"><h2 class="h5 fw-semibold mb-0">Storage usage</h2><span class="cloud-muted small">{{ $formatBytes($usedBytes) }} of {{ $formatBytes($totalBytes) }} used</span></div>
        <div class="progress cloud-storage-progress" role="progressbar" aria-label="Storage usage" aria-valuenow="{{ $usedPercentage }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ $usedPercentage }}%"></div></div>
        <div class="cloud-muted small mt-2">{{ $usedPercentage }}% used · {{ $formatBytes($remainingBytes) }} free</div>
    </div></section>

    <section class="card cloud-card"><div class="card-body">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3"><div><h2 class="h5 fw-semibold mb-1">Recent files</h2><p class="cloud-muted small mb-0">Your five most recently uploaded files</p></div><a class="btn btn-outline-primary btn-sm" href="{{ route('files.index') }}">View all</a></div>
        @if ($recentFiles->isEmpty())
            <div class="cloud-empty-state text-center"><i class="bi bi-folder2-open" aria-hidden="true"></i><p class="fw-semibold mt-2 mb-1">No files yet</p><p class="cloud-muted small mb-3">Upload your first file to get started.</p><a href="{{ route('files.create') }}" class="btn btn-primary btn-sm">Upload a file</a></div>
        @else
            <div class="table-responsive"><table class="table table-hover cloud-dashboard-table align-middle mb-0"><thead><tr><th scope="col">File</th><th scope="col">Size</th><th scope="col">Uploaded</th><th scope="col"><span class="visually-hidden">Action</span></th></tr></thead><tbody>
            @foreach ($recentFiles as $file)
                <tr><td><i class="bi bi-file-earmark text-primary me-2" aria-hidden="true"></i><span class="cloud-file-name">{{ $file->original_name }}</span></td><td class="text-nowrap">{{ $formatBytes((int) $file->size_bytes) }}</td><td class="text-nowrap">{{ $file->created_at->format('d M Y') }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('files.download', $file) }}">Download</a></td></tr>
            @endforeach
            </tbody></table></div>
        @endif
    </div></section>
</x-cloud-layout>
