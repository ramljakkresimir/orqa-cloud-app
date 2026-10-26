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

        <p>Welcome, {{ auth()->user()->name }}!</p>

        <div class="card mt-4">
            <div class="card-body">
                <h5>Available Storage</h5>

                <p>
                    {{ number_format(
                        auth()->user()->remainingStorageBytes()
                        / 1024 / 1024 / 1024,
                        2
                    ) }} GiB remaining
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