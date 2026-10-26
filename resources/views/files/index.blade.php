<x-cloud-layout :title="$isSuperAdmin ? 'All Files' : 'My Files'">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
        <div>
            <div class="cloud-section-eyebrow mb-2">CLOUD STORAGE</div>
            <h1 class="h2 fw-bold mb-1">{{ $isSuperAdmin ? 'All files' : 'My files' }}</h1>
            <p class="cloud-muted mb-0">{{ $isSuperAdmin ? 'Manage files uploaded by all users.' : 'Find, download and manage your documents.' }}</p>
        </div>
        @unless ($isSuperAdmin)
            <a href="{{ route('files.create') }}" class="btn btn-primary cloud-action-btn">
                <i class="bi bi-cloud-arrow-up me-2" aria-hidden="true"></i>Upload file
            </a>
        @endunless
    </div>

    <section class="card cloud-card border-0" aria-label="Files list">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="h5 fw-semibold mb-0">Documents</h2>
                <span class="cloud-count-pill">{{ $files->total() }} {{ $files->total() === 1 ? 'file' : 'files' }}</span>
            </div>
            @if ($files->isEmpty())
                <div class="cloud-empty-state text-center">
                    <i class="bi bi-folder2-open d-block mb-2" aria-hidden="true"></i>
                    <p class="fw-semibold mb-1">No files found</p>
                    <p class="cloud-muted mb-3">{{ $isSuperAdmin ? 'There are no uploaded files yet.' : 'Upload your first document to get started.' }}</p>
                    @unless ($isSuperAdmin)
                        <a href="{{ route('files.create') }}" class="btn btn-primary">Upload a file</a>
                    @endunless
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle cloud-dashboard-table cloud-files-table mb-0">
                        <thead><tr>
                            <th scope="col">File name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Size</th>
                            <th scope="col">Uploaded</th>
                            @if ($isSuperAdmin)<th scope="col">Added By</th>@endif
                            <th scope="col" class="text-end">Actions</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($files as $file)
                                @php
                                    $extension = strtolower($file->extension ?? '');
                                    $iconClass = match ($extension) {
                                        'pdf' => 'bi-file-earmark-pdf-fill text-danger',
                                        'doc', 'docx' => 'bi-file-earmark-word-fill text-primary',
                                        'xls', 'xlsx', 'csv' => 'bi-file-earmark-excel-fill text-success',
                                        'ppt', 'pptx' => 'bi-file-earmark-ppt-fill text-warning',
                                        'jpg', 'jpeg', 'png', 'gif', 'webp' => 'bi-file-earmark-image-fill text-info',
                                        'zip', 'rar', '7z' => 'bi-file-earmark-zip-fill text-secondary',
                                        'txt' => 'bi-file-earmark-text-fill text-secondary',
                                        default => 'bi-file-earmark-fill text-secondary',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="cloud-file-icon"><i class="bi {{ $iconClass }}" aria-hidden="true"></i></span>
                                            <span class="cloud-file-name fw-medium text-break">{{ $file->original_name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="cloud-type-badge">{{ strtoupper($file->extension ?: 'FILE') }}</span></td>
                                    <td class="text-nowrap">{{ number_format($file->size_bytes / 1024 / 1024, 2) }} MB</td>
                                    <td class="text-nowrap">{{ $file->created_at->format('d.m.Y. H:i') }}</td>
                                    @if ($isSuperAdmin)<td>{{ $file->user?->username ?? 'Unknown' }}</td>@endif
                                    <td>
                                        <div class="d-flex gap-2 justify-content-end flex-wrap">
                                            <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-outline-primary cloud-table-action" aria-label="Download {{ $file->original_name }}">
                                                <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                            </a>
                                            <form action="{{ route('files.destroy', $file) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this file?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger cloud-table-action" aria-label="Delete {{ $file->original_name }}">
                                                    <i class="bi bi-trash me-1" aria-hidden="true"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($files->hasPages())
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-4">
                        <small class="cloud-muted">Showing {{ $files->firstItem() }}–{{ $files->lastItem() }} of {{ $files->total() }}</small>
                        <div class="cloud-file-pagination">{{ $files->links('pagination::bootstrap-5') }}</div>
                    </div>
                @endif
            @endif
        </div>
    </section>
</x-cloud-layout>
