<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Мой сайт')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            padding-top: 56px; /* Для фиксированного навбара */
        }

        .main-content {
            min-height: calc(100vh - 120px); /* Чтобы футер был внизу */
        }
    </style>

    @stack('styles')
</head>
<body>
<!-- Простейший навбар -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">🍳 Рецепты</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('recipes.index') }}">Все рецепты</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Основное содержимое -->
<main class="main-content">
    <div class="container py-4">
        @yield('content')
    </div>
</main>

<!-- Простой футер -->
<footer class="bg-dark text-white py-3">
    <div class="container text-center">
        <p class="mb-0">© {{ date('Y') }} Кулинарная книга. Все права защищены.</p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Кастомные скрипты -->
@stack('scripts')
</body>
</html>
