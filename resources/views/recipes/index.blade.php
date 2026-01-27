@extends('layouts.app')

@section('title', 'Рецепты - Кулинарная книга')

@section('meta-description', 'Коллекция вкусных рецептов на любой вкус. Простые и сложные рецепты с пошаговыми инструкциями.')

@section('content')
    <div class="container py-5">
        <!-- Заголовок и кнопка создания -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-2">🍳 Все рецепты</h1>
            </div>

            @auth
                <a href="{{ route('recipes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Добавить рецепт
                </a>
            @endauth
        </div>

        <!-- Фильтры и поиск -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('recipes.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Поиск</label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   id="search"
                                   name="search"
                                   placeholder="Название или ингредиенты..."
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <label for="difficulty" class="form-label">Сложность</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="">Любая</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Легкая</option>
                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Средняя</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Сложная</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            Применить
                        </button>
                    </div>
                </form>

                <!-- Быстрые фильтры -->
                <div class="mt-3">
                    <div class="btn-group" role="group">
                        <a href="{{ route('recipes.index', ['sort' => 'latest']) }}"
                           class="btn btn-sm btn-outline-secondary {{ request('sort') == 'latest' ? 'active' : '' }}">
                            Новые
                        </a>
                        <a href="{{ route('recipes.index', ['sort' => 'popular']) }}"
                           class="btn btn-sm btn-outline-secondary {{ request('sort') == 'popular' ? 'active' : '' }}">
                            Популярные
                        </a>
                        <a href="{{ route('recipes.index', ['vegetarian' => 1]) }}"
                           class="btn btn-sm btn-outline-success {{ request('vegetarian') ? 'active' : '' }}">
                            Вегетарианские
                        </a>
                        <a href="{{ route('recipes.index', ['quick' => 1]) }}"
                           class="btn btn-sm btn-outline-warning {{ request('quick') ? 'active' : '' }}">
                            Быстрые
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Сообщения об успехе -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Сетка рецептов -->
        @if($recipes->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($recipes as $recipe)
                    <div class="col">
                        <div class="card h-100 recipe-card">
                            <!-- Бейджы -->
                            <div class="position-absolute top-0 start-0 p-3">
                                @if($recipe->is_featured)
                                    <span class="badge bg-warning">⭐ Избранное</span>
                                @endif
                                @if($recipe->is_vegetarian)
                                    <span class="badge bg-success">🌿 Вегетарианское</span>
                                @endif
                            </div>

                            <!-- Изображение -->
                            <div class="card-img-top position-relative" style="height: 200px; overflow: hidden;">
                                @if($recipe->image_path)
                                    <img src="{{ $recipe->full_image_url }}"
                                         alt="{{ $recipe->title }}"
                                         class="img-fluid w-100 h-100 object-fit-cover">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                        <i class="bi bi-image display-4"></i>
                                    </div>
                                @endif

                                <!-- Сложность -->
                                <div class="position-absolute bottom-0 end-0 m-2">
                                <span class="badge bg-{{ $recipe->difficulty == 'easy' ? 'success' : ($recipe->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                    {{ $recipe->difficulty == 'easy' ? 'Легко' : ($recipe->difficulty == 'medium' ? 'Средне' : 'Сложно') }}
                                </span>
                                </div>
                            </div>

                            <!-- Тело карточки -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="{{ route('recipes.show', $recipe->slug) }}"
                                       class="text-decoration-none text-dark">
                                        {{ $recipe->title }}
                                    </a>
                                </h5>

                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($recipe->excerpt, 100) }}
                                </p>

                                <!-- Мета-информация -->
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $recipe->total_time }} мин
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-people me-1"></i>
                                            {{ $recipe->servings }} порций
                                        </small>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            @if($recipe->user->avatar)
                                                <img src="{{ $recipe->user->avatar_url }}"
                                                     alt="{{ $recipe->user->name }}"
                                                     class="rounded-circle me-2"
                                                     style="width: 24px; height: 24px;">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                                                     style="width: 24px; height: 24px; font-size: 12px;">
                                                    {{ Str::upper(Str::substr($recipe->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <small>{{ $recipe->user->name }}</small>
                                        </div>

                                        <div class="text-muted">
                                            <i class="bi bi-eye me-1"></i>{{ $recipe->views_count }}
                                            <i class="bi bi-heart ms-2 me-1"></i>{{ $recipe->likes_count }}
                                            <i class="bi bi-star ms-2 me-1"></i>{{ number_format($recipe->rating, 1) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Футер карточки -->
                            <div class="card-footer bg-transparent border-top-0 pt-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        {{ $recipe->created_at->diffForHumans() }}
                                    </small>

                                    @if($recipe->category)
                                        <a href="{{ route('categories.show', $recipe->category->slug) }}"
                                           class="badge bg-light text-decoration-none text-dark">
                                            {{ $recipe->category->name }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            <div class="mt-5">
                {{ $recipes->links() }}
            </div>
        @else
            <!-- Пустой список -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-emoji-frown display-1 text-muted"></i>
                </div>
                <h3 class="h4 mb-3">Рецепты не найдены</h3>
                <a href="{{ route('recipes.index') }}" class="btn btn-outline-primary">
                    Сбросить фильтры
                </a>
            </div>
        @endif
    </div>

    <!-- Стили для карточек -->
    <style>
        .recipe-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .recipe-card .card-img-top {
            border-radius: 8px 8px 0 0;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .pagination {
            justify-content: center;
        }

        .badge {
            font-weight: 500;
        }
    </style>

    <!-- Скрипт для автосабмита фильтров -->
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Автосабмит при изменении select
                const selects = ['category', 'difficulty'];
                selects.forEach(function(id) {
                    const element = document.getElementById(id);
                    if (element) {
                        element.addEventListener('change', function() {
                            this.form.submit();
                        });
                    }
                });

                // Подсветка активных фильтров
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.toString()) {
                    document.querySelectorAll('.btn-group .btn').forEach(btn => {
                        if (btn.classList.contains('active')) {
                            btn.classList.remove('btn-outline-secondary');
                            btn.classList.add('btn-primary');
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
