<x-cloud-layout title="Admin Dashboard">
    <div class="mb-4">
        <div class="text-uppercase small fw-semibold cloud-muted mb-2">Workspace overview</div>
        <h1 class="fw-bold mb-2">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="cloud-muted mb-0">Manage files and review user storage from one place.</p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-6">
            <section class="card cloud-card h-100">
                <div class="card-body">
                    <div class="fs-2 text-primary mb-3"><i class="bi bi-folder2-open" aria-hidden="true"></i></div>
                    <h2 class="h5 fw-semibold">All Files</h2>
                    <p class="cloud-muted">Browse, download, and manage uploaded files.</p>
                    <a href="{{ route('files.index') }}" class="btn btn-primary">View All Files</a>
                </div>
            </section>
        </div>
        <div class="col-12 col-md-6">
            <section class="card cloud-card h-100">
                <div class="card-body">
                    <div class="fs-2 text-primary mb-3"><i class="bi bi-people" aria-hidden="true"></i></div>
                    <h2 class="h5 fw-semibold">User Storage</h2>
                    <p class="cloud-muted">See users and their available storage quota.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">View User Storage</a>
                </div>
            </section>
        </div>
    </div>
</x-cloud-layout>
