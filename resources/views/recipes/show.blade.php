@extends('layouts.app')

@section('title', $recipe->title . ' - Кулинарная книга')

@section('meta-description', $recipe->meta_description ?? Str::limit($recipe->description, 160))

@if($recipe->image_path)
    @section('meta-image', $recipe->full_image_url)
@endif

@section('content')
    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
                <li class="breadcrumb-item"><a href="{{ route('recipes.index') }}">Рецепты</a></li>
                @if($recipe->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('categories.show', $recipe->category->slug) }}">
                            {{ $recipe->category->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($recipe->title, 30) }}</li>
            </ol>
        </nav>

        <!-- Сообщения -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Основная информация -->
        <div class="row">
            <!-- Левая колонка - изображение и ингредиенты -->
            <div class="col-lg-4 mb-4">
                <!-- Изображение -->
                <div class="card mb-4">
                    @if($recipe->image_path)
                        <img src="{{ $recipe->full_image_url }}"
                             alt="{{ $recipe->title }}"
                             class="card-img-top"
                             style="max-height: 400px; object-fit: cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light text-muted"
                             style="height: 300px;">
                            <i class="bi bi-image display-1"></i>
                        </div>
                    @endif

                    <!-- Действия -->
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <!-- Лайк -->
                            <form action="{{ route('recipes.like', $recipe) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit"
                                        class="btn btn-outline-danger {{ $recipe->isLikedBy(auth()->user()) ? 'active' : '' }}"
                                    {{ auth()->guest() ? 'disabled' : '' }}>
                                    <i class="bi bi-heart{{ $recipe->isLikedBy(auth()->user()) ? '-fill' : '' }}"></i>
                                    <span class="ms-1">{{ $recipe->likes_count }}</span>
                                </button>
                            </form>

                            <!-- Сохранение -->
                            @auth
                                <form action="{{ route('recipes.save', $recipe) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-outline-primary {{ $recipe->isSavedBy(auth()->user()) ? 'active' : '' }}">
                                        <i class="bi bi-bookmark{{ $recipe->isSavedBy(auth()->user()) ? '-fill' : '' }}"></i>
                                        Сохранить
                                    </button>
                                </form>
                            @endauth

                            <!-- Поделиться -->
                            <div class="dropdown d-inline">
                                <button class="btn btn-outline-secondary dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-share"></i> Поделиться
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                           href="https://t.me/share/url?url={{ url()->current() }}&text={{ $recipe->title }}"
                                           target="_blank">
                                            <i class="bi bi-telegram me-2"></i>Telegram
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                           href="https://vk.com/share.php?url={{ url()->current() }}&title={{ $recipe->title }}&description={{ $recipe->excerpt }}"
                                           target="_blank">
                                            <i class="bi bi-vimeo me-2"></i>VKontakte
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" onclick="copyToClipboard('{{ url()->current() }}')">
                                            <i class="bi bi-link-45deg me-2"></i>Копировать ссылку
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ингредиенты -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📝 Ингредиенты</h5>
                        <small class="text-muted">На {{ $recipe->servings }} порций</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Количество порций:</label>
                            <div class="input-group input-group-sm" style="max-width: 150px;">
                                <button class="btn btn-outline-secondary" type="button" id="decrease-portions">-</button>
                                <input type="number"
                                       class="form-control text-center"
                                       id="portions"
                                       value="{{ $recipe->servings }}"
                                       min="1">
                                <button class="btn btn-outline-secondary" type="button" id="increase-portions">+</button>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush" id="ingredients-list">
                            @if(is_array($recipe->ingredients))
                                @foreach($recipe->ingredients as $ingredient)
                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0 py-2">
                                        <div>
                                            <input class="form-check-input me-2" type="checkbox" id="ingredient-{{ $loop->index }}">
                                            <label class="form-check-label stretched-link"
                                                   for="ingredient-{{ $loop->index }}">
                                                {{ $ingredient['name'] }}
                                            </label>
                                        </div>
                                        <span class="ingredient-amount"
                                              data-original="{{ $ingredient['amount'] }}">
                                        {{ $ingredient['amount'] }}
                                    </span>
                                    </li>
                                @endforeach
                            @else
                                <li class="list-group-item border-0 px-0 py-2">
                                    <div class="alert alert-info mb-0">
                                        Ингредиенты не указаны
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Информация о рецепте -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">ℹ️ Информация</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-clock me-2 text-primary"></i>
                                <strong>Время приготовления:</strong>
                                <span class="float-end">
                                {{ $recipe->total_time }} мин
                            </span>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-people me-2 text-primary"></i>
                                <strong>Порций:</strong>
                                <span class="float-end">{{ $recipe->servings }}</span>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-bar-chart me-2 text-primary"></i>
                                <strong>Сложность:</strong>
                                <span class="float-end">
                                @if($recipe->difficulty == 'easy')
                                        <span class="text-success">Легкая</span>
                                    @elseif($recipe->difficulty == 'medium')
                                        <span class="text-warning">Средняя</span>
                                    @else
                                        <span class="text-danger">Сложная</span>
                                    @endif
                            </span>
                            </li>
                            @if($recipe->is_vegetarian)
                                <li class="mb-2">
                                    <i class="bi bi-flower1 me-2 text-success"></i>
                                    <strong>Вегетарианское:</strong>
                                    <span class="float-end text-success">Да</span>
                                </li>
                            @endif
                            @if($recipe->is_vegan)
                                <li class="mb-2">
                                    <i class="bi bi-tree me-2 text-success"></i>
                                    <strong>Веганское:</strong>
                                    <span class="float-end text-success">Да</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Правая колонка - описание и инструкции -->
            <div class="col-lg-8">
                <!-- Заголовок и мета-информация -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h2 mb-2">{{ $recipe->title }}</h1>
                            <div class="d-flex align-items-center text-muted mb-3">
                                <div class="d-flex align-items-center me-3">
                                    @if($recipe->user->avatar)
                                        <img src="{{ $recipe->user->avatar_url }}"
                                             alt="{{ $recipe->user->name }}"
                                             class="rounded-circle me-2"
                                             style="width: 32px; height: 32px;">
                                    @endif
                                    <span>{{ $recipe->user->name }}</span>
                                </div>
                                <div class="me-3">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ $recipe->created_at->format('d.m.Y') }}
                                </div>
                                <div>
                                    <i class="bi bi-eye me-1"></i>
                                    {{ $recipe->views_count }} просмотров
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки управления для автора/админа -->
                        @can('update', $recipe)
                            <div class="dropdown">
                                <button class="btn btn-light border dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('recipes.edit', $recipe) }}">
                                            <i class="bi bi-pencil me-2"></i>Редактировать
                                        </a>
                                    </li>
                                    @if(!$recipe->is_published)
                                        <li>
                                            <form action="{{ route('recipes.publish', $recipe) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-check-circle me-2"></i>Опубликовать
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('recipes.destroy', $recipe) }}"
                                              method="POST"
                                              onsubmit="return confirm('Удалить рецепт?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Удалить
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endcan
                    </div>

                    <!-- Бейджы -->
                    <div class="mb-4">
                        @if($recipe->is_featured)
                            <span class="badge bg-warning mb-2 me-2">⭐ Избранное</span>
                        @endif
                        @if($recipe->category)
                            <a href="{{ route('categories.show', $recipe->category->slug) }}"
                               class="badge bg-primary text-decoration-none mb-2 me-2">
                                {{ $recipe->category->name }}
                            </a>
                        @endif
                        @foreach($recipe->tags as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}"
                               class="badge bg-light text-dark text-decoration-none mb-2 me-2">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Описание -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">📖 Описание</h5>
                        <div class="recipe-description">
                            {!! nl2br(e($recipe->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Инструкции приготовления -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">👨‍🍳 Инструкции приготовления</h5>

                        @if(is_array($recipe->instructions))
                            <div class="steps">
                                @foreach($recipe->instructions as $step)
                                    <div class="step mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex">
                                            <div class="step-number me-3">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <strong>{{ $loop->iteration }}</strong>
                                                </div>
                                            </div>
                                            <div class="step-content flex-grow-1">
                                                <h6 class="mb-2">Шаг {{ $loop->iteration }}</h6>
                                                <p class="mb-0">{{ $step['text'] }}</p>

                                                @if(!empty($step['image']))
                                                    <img src="{{ $step['image'] }}"
                                                         alt="Шаг {{ $loop->iteration }}"
                                                         class="img-fluid rounded mt-3"
                                                         style="max-height: 300px;">
                                                @endif

                                                @if(!empty($step['tip']))
                                                    <div class="alert alert-info mt-3 mb-0">
                                                        <i class="bi bi-lightbulb me-2"></i>
                                                        <strong>Совет:</strong> {{ $step['tip'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                Инструкции не указаны
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Комментарии -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            💬 Комментарии
                            <span class="badge bg-secondary">{{ $recipe->comments_count }}</span>
                        </h5>

                        <!-- Форма добавления комментария -->
                        @auth
                            <form action="{{ route('comments.store', $recipe) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                <textarea class="form-control"
                                          name="content"
                                          rows="3"
                                          placeholder="Ваш комментарий..."
                                          required></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-2"></i>Отправить
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info mb-4">
                                <a href="{{ route('login') }}" class="alert-link">Войдите</a>, чтобы оставить комментарий
                            </div>
                        @endauth

                        <!-- Список комментариев -->
                        @if($recipe->comments->count() > 0)
                            <div class="comments">
                                @foreach($recipe->comments as $comment)
                                    <div class="comment mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex">
                                            <!-- Аватар -->
                                            <div class="me-3">
                                                @if($comment->user->avatar)
                                                    <img src="{{ $comment->user->avatar_url }}"
                                                         alt="{{ $comment->user->name }}"
                                                         class="rounded-circle"
                                                         style="width: 40px; height: 40px;">
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px;">
                                                        {{ Str::upper(Str::substr($comment->user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Контент -->
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <strong class="me-2">{{ $comment->user->name }}</strong>
                                                        <small class="text-muted">
                                                            {{ $comment->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>

                                                    @can('delete', $comment)
                                                        <form action="{{ route('comments.destroy', $comment) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Удалить комментарий?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-link text-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>

                                                <p class="mb-2">{{ $comment->content }}</p>

                                                <!-- Ответы -->
                                                @if($comment->replies->count() > 0)
                                                    <div class="replies mt-3 ps-4 border-start">
                                                        @foreach($comment->replies as $reply)
                                                            <div class="reply mb-3">
                                                                <div class="d-flex">
                                                                    <div class="me-3">
                                                                        @if($reply->user->avatar)
                                                                            <img src="{{ $reply->user->avatar_url }}"
                                                                                 alt="{{ $reply->user->name }}"
                                                                                 class="rounded-circle"
                                                                                 style="width: 30px; height: 30px;">
                                                                        @else
                                                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                                                                 style="width: 30px; height: 30px; font-size: 12px;">
                                                                                {{ Str::upper(Str::substr($reply->user->name, 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div>
                                                                        <div class="mb-1">
                                                                            <strong class="me-2">{{ $reply->user->name }}</strong>
                                                                            <small class="text-muted">
                                                                                {{ $reply->created_at->diffForHumans() }}
                                                                            </small>
                                                                        </div>
                                                                        <p class="mb-0 small">{{ $reply->content }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <!-- Форма ответа -->
                                                @auth
                                                    <form action="{{ route('comments.reply', $comment) }}"
                                                          method="POST"
                                                          class="mt-3">
                                                        @csrf
                                                        <div class="input-group input-group-sm">
                                                            <input type="text"
                                                                   class="form-control"
                                                                   name="content"
                                                                   placeholder="Напишите ответ..."
                                                                   required>
                                                            <button class="btn btn-outline-primary" type="submit">
                                                                Ответить
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-chat-dots display-1 text-muted mb-3"></i>
                                <p class="text-muted">Пока нет комментариев. Будьте первым!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Стили -->
    <style>
        .step-number {
            flex-shrink: 0;
        }

        .recipe-description {
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .recipe-description p {
            margin-bottom: 1rem;
        }

        .step-content h6 {
            color: #333;
            font-weight: 600;
        }

        .comments .replies {
            border-color: #e9ecef !important;
        }

        .form-check-label {
            cursor: pointer;
        }

        .form-check-input:checked + .form-check-label {
            text-decoration: line-through;
            color: #6c757d;
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

    <!-- Скрипты -->
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Копирование ссылки
                function copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(function() {
                        alert('Ссылка скопирована!');
                    });
                }

                // Управление количеством порций
                const portionsInput = document.getElementById('portions');
                const decreaseBtn = document.getElementById('decrease-portions');
                const increaseBtn = document.getElementById('increase-portions');

                if (decreaseBtn && increaseBtn && portionsInput) {
                    decreaseBtn.addEventListener('click', function() {
                        let value = parseInt(portionsInput.value) - 1;
                        if (value < 1) value = 1;
                        portionsInput.value = value;
                        updateIngredients(value);
                    });

                    increaseBtn.addEventListener('click', function() {
                        portionsInput.value = parseInt(portionsInput.value) + 1;
                        updateIngredients(portionsInput.value);
                    });

                    portionsInput.addEventListener('change', function() {
                        if (this.value < 1) this.value = 1;
                        updateIngredients(this.value);
                    });
                }

                // Обновление количества ингредиентов
                function updateIngredients(portions) {
                    const originalPortions = {{ $recipe->servings }};
                    const multiplier = portions / originalPortions;

                    document.querySelectorAll('.ingredient-amount').forEach(function(el) {
                        const original = el.dataset.original;
                        // Простая логика - если есть число, умножаем его
                        const match = original.match(/(\d+(?:\.\d+)?)/);
                        if (match) {
                            const number = parseFloat(match[1]);
                            const newNumber = (number * multiplier).toFixed(1).replace('.0', '');
                            el.textContent = original.replace(match[1], newNumber);
                        }
                    });
                }

                // Плавная прокрутка к комментариям
                if (window.location.hash === '#comments') {
                    document.querySelector('.comments').scrollIntoView({ behavior: 'smooth' });
                }
            });
        </script>
    @endpush
@endsection
