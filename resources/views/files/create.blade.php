<x-cloud-layout title="Upload File">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
        <div>
            <div class="cloud-section-eyebrow mb-2">CLOUD STORAGE</div>
            <h1 class="h2 fw-bold mb-1">Upload a file</h1>
            <p class="cloud-muted mb-0">Add a document securely to your private cloud storage.</p>
        </div>
        <a href="{{ route('files.index') }}" class="btn btn-outline-secondary cloud-action-btn">
            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>My files
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <section class="card cloud-card border-0">
                <div class="card-body">
                    <h2 class="h5 fw-semibold mb-2">Select your document</h2>
                    <p class="cloud-muted mb-4">Files are stored privately and accessible only to you and the super administrator.</p>
                    <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="cloud-upload-zone mb-3">
                            <i class="bi bi-cloud-arrow-up cloud-upload-icon d-block mb-3" aria-hidden="true"></i>
                            <label for="document" class="form-label fw-semibold">Choose a file to upload</label>
                            <input type="file" name="document" id="document" required
                                   class="form-control @error('document') is-invalid @enderror"
                                   aria-describedby="uploadHelp">
                            @error('document')
                                <div class="invalid-feedback text-start" role="alert">{{ $message }}</div>
                            @enderror
                            <div id="uploadHelp" class="form-text mt-3">
                                Maximum 20 MB per file. PDF, Office documents, TXT, JPG, PNG and ZIP.
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary cloud-action-btn">
                                <i class="bi bi-cloud-arrow-up me-2" aria-hidden="true"></i>Upload file
                            </button>
                            <a href="{{ route('files.index') }}" class="btn btn-outline-secondary cloud-action-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
        <div class="col-12 col-xl-4">
            <aside class="card cloud-card border-0">
                <div class="card-body">
                    <div class="cloud-stat-icon mb-3"><i class="bi bi-hdd" aria-hidden="true"></i></div>
                    <h2 class="h6 fw-semibold mb-2">Available storage</h2>
                    <p class="h3 fw-bold mb-2">{{ number_format($remainingBytes / 1024 / 1024 / 1024, 2) }} GB</p>
                    <p class="cloud-muted small mb-0">{{ number_format($remainingBytes / 1024 / 1024, 2) }} MB remaining out of your allocated space.</p>
                </div>
            </aside>
        </div>
    </div>
</x-cloud-layout>
