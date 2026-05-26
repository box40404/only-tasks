<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yandex.Disk Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; color: #212529; font-family: system-ui, -apple-system, sans-serif; }
        .navbar { background-color: #ffffff !important; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .card { border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02); border-radius: 12px; }
        .btn { border-radius: 8px; font-weight: 500; }
    </style>
    @vite('resources/js/app.js')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container">
            <button class="navbar-expand-lg navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="navbar-item">
                        <a class="nav-link d-flex align-items-center {{ Request::is('/') ? 'active fw-bold text-primary' : '' }}" href="/">
                            <i class="bi bi-folder2-open me-1"></i> Мои файлы
                        </a>
                    </li>
                    <li class="navbar-item ms-lg-3">
                        <a class="nav-link d-flex align-items-center {{ Request::is('upload') ? 'active fw-bold text-primary' : '' }}" href="/upload">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Загрузка файлов
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>