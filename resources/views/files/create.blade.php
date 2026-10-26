<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Upload File - ORQA Cloud</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">
    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Upload New File</h1>

            <a href="{{ route('user.dashboard') }}"
               class="btn btn-outline-secondary">
                Back to Dashboard
            </a>
        </div>

        <div class="card">
            <div class="card-body">

                <p>
                    Available space:
                    <strong>
                        {{ number_format(
                            $remainingBytes / 1024 / 1024 / 1024,
                            2
                        ) }} GiB
                    </strong>
                </p>

                <form
                    method="POST"
                    action="{{ route('files.store') }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div class="mb-3">
                        <label for="document" class="form-label">
                            Select a file
                        </label>

                        <input
                            type="file"
                            name="document"
                            id="document"
                            required
                            class="form-control @error('document') is-invalid @enderror"
                        >

                        @error('document')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Maximum file size: 20 MiB.
                            Supported: PDF, Office, TXT, JPG, PNG, ZIP.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Upload File
                    </button>
                </form>

            </div>
        </div>

    </div>
</body>
</html>