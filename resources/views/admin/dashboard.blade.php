<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - ORQA Cloud</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1>Super Administrator Dashboard</h1>

        <p>Welcome, {{ auth()->user()->name }}!</p>

        <div class="alert alert-info">
            Here we will display all uploaded files
            and user storage information.
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                    class="btn btn-outline-danger">
                Logout
            </button>
        </form>
    </div>
</body>
</html>