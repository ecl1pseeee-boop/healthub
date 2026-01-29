@extends('layouts.app')

@section('title', 'Рецепты - Кулинарная книга')

@section('content')
    <div class="container py-5">
        <!-- Заголовок и кнопка создания -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="display-5 fw-bold mb-2">Все рецепты</h1>
                <p class="text-muted mb-0">
                    @if(request()->has('search'))
                        Результаты поиска "{{ request('search') }}"
                    @endif
                </p>
            </div>

            @auth
                <a href="{{ route('recipes.create') }}" class="btn btn-primary btn-lg shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>Добавить рецепт
                </a>
            @endauth
        </div>

        <!-- Поиск и фильтры -->
        <div class="card shadow-sm mb-5 border-0">
            <div class="card-body p-4">
                <form action="{{ route('recipes.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   class="form-control border-start-0"
                                   placeholder="Поиск рецептов..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="difficulty" class="form-select">
                            <option value="">Все уровни сложности</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Легкий</option>
                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Средний</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Сложный</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-funnel me-2"></i>Применить
                            </button>
                            <a href="{{ route('recipes.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Быстрые фильтры -->
                <div class="mt-3 d-flex flex-wrap gap-2">
                    <a href="{{ route('recipes.index', ['difficulty' => 'easy']) }}"
                       class="badge bg-success bg-opacity-10 text-success text-decoration-none py-2 px-3">
                        <i class="bi bi-emoji-smile me-1"></i>Легкие
                    </a>
                    <a href="{{ route('recipes.index', ['is_vegan' => true]) }}"
                       class="badge bg-info bg-opacity-10 text-info text-decoration-none py-2 px-3">
                        <i class="bi bi-tree me-1"></i>Вегетарианские
                    </a>
                    <a href="{{ route('recipes.index', ['sort' => 'views']) }}"
                       class="badge bg-warning bg-opacity-10 text-warning text-decoration-none py-2 px-3">
                        <i class="bi bi-fire me-1"></i>Популярные
                    </a>
                </div>
            </div>
        </div>

        <!-- Сетка рецептов -->
        @if($recipes->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                @foreach($recipes as $recipe)
                    <div class="col">
                        <div class="card h-100 recipe-card border-0 shadow-sm hover-shadow">
                            <!-- Изображение рецепта -->
                            <div class="position-relative">
                                @if($recipe->image_path)
                                    <img src="{{ $recipe->full_image_url }}"
                                         class="card-img-top recipe-image"
                                         alt="{{ $recipe->title }}"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                         style="height: 200px;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif

                                <!-- Бейджи на изображении -->
                                <div class="position-absolute top-0 start-0 p-3">
                                    <span class="badge bg-{{ $recipe->difficulty == 'easy' ? 'success' : ($recipe->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                        {{ $recipe->difficulty == 'easy' ? 'Легко' : ($recipe->difficulty == 'medium' ? 'Средне' : 'Сложно') }}
                                    </span>
                                </div>

                                @if($recipe->is_vegan)
                                    <div class="position-absolute top-0 end-0 p-3">
                                        <span class="badge bg-info">
                                            <i class="bi bi-tree me-1"></i>Веган
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Тело карточки -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2">
                                    <a href="{{ route('recipes.show', $recipe) }}"
                                       class="text-dark text-decoration-none stretched-link">
                                        {{ Str::limit($recipe->title, 40) }}
                                    </a>
                                </h5>

                                <p class="card-text text-muted small mb-3 flex-grow-1">
                                    {{ Str::limit($recipe->description, 100) }}
                                </p>

                                <!-- Мета-информация -->
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <div class="d-flex gap-3 text-muted small">
                                        <span class="d-flex align-items-center gap-1">
                                            <i class="bi bi-clock"></i>
                                            {{ $recipe->total_time }} мин
                                        </span>
                                        <span class="d-flex align-items-center gap-1">
                                            <i class="bi bi-people"></i>
                                            {{ $recipe->servings }} порц.
                                        </span>
                                    </div>

                                    <div class="text-muted small">
                                        <i class="bi bi-eye me-1"></i>{{ $recipe->views_count }}
                                    </div>
                                </div>

                                <!-- Автор и дата -->
                                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            @if($recipe->user->avatar)
                                                <img src="{{ asset('storage/' . $recipe->user->avatar) }}"
                                                     class="rounded-circle"
                                                     width="32"
                                                     height="32"
                                                     alt="{{ $recipe->user->name }}">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                                     style="width: 32px; height: 32px;">
                                                    {{ Str::substr($recipe->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="small">
                                            <div class="fw-medium">{{ $recipe->user->name }}</div>
                                            <div class="text-muted">
                                                {{ $recipe->published_at?->diffForHumans() ?? $recipe->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Рейтинг -->
                                    <div class="text-warning">
                                        @php
                                            $totalVotes = $recipe->rate_likes_count + $recipe->rate_medium_count + $recipe->rate_dislikes_count;
                                            $averageRating = $totalVotes > 0
                                                ? ($recipe->rate_likes_count * 5 + $recipe->rate_medium_count * 3) / $totalVotes
                                                : 0;
                                        @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= round($averageRating / 5 * 5) ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Футер карточки (действия) -->
                            @auth
                                <div class="card-footer bg-transparent border-top-0 pt-0">
                                    <div class="d-flex justify-content-between">
                                        @if(auth()->id() == $recipe->user_id)
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('recipes.edit', $recipe) }}"
                                                   class="btn btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $recipe->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-bookmark me-1"></i>В закладки
                                            </button>
                                        @endif

                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-success like-btn"
                                                    data-recipe-id="{{ $recipe->id }}"
                                                    data-action="like">
                                                <i class="bi bi-hand-thumbs-up"></i>
                                                <span class="like-count">{{ $recipe->rate_likes_count }}</span>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning like-btn"
                                                    data-recipe-id="{{ $recipe->id }}"
                                                    data-action="medium">
                                                <i class="bi bi-emoji-neutral"></i>
                                                <span class="medium-count">{{ $recipe->rate_medium_count }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Модальное окно удаления -->
                                <div class="modal fade" id="deleteModal{{ $recipe->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Подтверждение удаления</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Вы уверены, что хотите удалить рецепт "{{ $recipe->title }}"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                <form action="{{ route('recipes.destroy', $recipe) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Удалить</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            @if($recipes->hasPages())
                <div class="mt-5">
                    {{ $recipes->withQueryString()->links() }}
                </div>
            @endif
        @else
            <!-- Сообщение, если рецептов нет -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-emoji-frown display-1 text-muted"></i>
                </div>
                <h4 class="mb-3">Рецепты не найдены</h4>
                <p class="text-muted mb-4">
                    @if(request()->has('search'))
                        Попробуйте изменить параметры поиска
                    @else
                        Будьте первым, кто добавит рецепт!
                    @endif
                </p>
                @auth
                    <a href="{{ route('recipes.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Создать первый рецепт
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Войдите, чтобы добавить рецепт
                    </a>
                @endauth
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .recipe-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }

        .recipe-image {
            transition: transform 0.5s ease;
        }

        .recipe-card:hover .recipe-image {
            transform: scale(1.05);
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .like-btn.active {
            background-color: var(--bs-primary);
            color: white;
        }

        .hover-shadow {
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .stretched-link::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1;
            content: "";
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Обработка лайков
            document.querySelectorAll('.like-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const recipeId = this.dataset.recipeId;
                    const action = this.dataset.action;
                    const button = this;

                    // В реальном приложении здесь был бы AJAX запрос
                    // fetch(`/recipes/${recipeId}/rate`, {
                    //     method: 'POST',
                    //     headers: {
                    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    //         'Content-Type': 'application/json'
                    //     },
                    //     body: JSON.stringify({ action: action })
                    // })
                    // .then(response => response.json())
                    // .then(data => {
                    //     if(data.success) {
                    //         button.querySelector('span').textContent = data.count;
                    //         button.classList.add('active');
                    //     }
                    // });

                    // Демо-версия
                    const countSpan = button.querySelector('span');
                    let currentCount = parseInt(countSpan.textContent);
                    countSpan.textContent = currentCount + 1;
                    button.classList.add('active');

                    // Убираем активный класс с других кнопок того же рецепта
                    document.querySelectorAll(`.like-btn[data-recipe-id="${recipeId}"]`)
                        .forEach(btn => {
                            if(btn !== button) {
                                btn.classList.remove('active');
                            }
                        });
                });
            });

            // Фильтрация при изменении селектов
            document.querySelectorAll('select[name="difficulty"], select[name="category"]').forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        });
    </script>
@endpush
