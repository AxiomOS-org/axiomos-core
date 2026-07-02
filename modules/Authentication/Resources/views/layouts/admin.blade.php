<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AxiomOS Authentication' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; min-height: 100vh; }
        .auth-shell { max-width: 520px; margin: 4rem auto; }
        .card { border: 0; box-shadow: 0 1px 3px rgba(15, 23, 42, .08); }
    </style>
</head>
<body>
<main class="auth-shell">
    @yield('content')
</main>
</body>
</html>
