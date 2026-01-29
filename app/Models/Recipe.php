<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory;

    protected $table = 'recipes';

    protected $fillable = [
        'title',
        'description',
        'instructions',
        'prep_time',
        'cook_time',
        'servings',
        'difficulty',
        'image_path',
        'is_published',
        'is_vegan',
        'user_id',
        'published_at',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'prep_time' => 'integer',
        'cook_time' => 'integer',
        'servings' => 'integer',
        'is_published' => 'boolean',
        'is_vegan' => 'boolean',
        'published_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'datetime',
        'rate_likes_count' => 'integer',
        'rate_medium_count' => 'integer',
        'rate_dislikes_count' => 'integer',
        'views_count' => 'integer',
    ];

    protected $attributes = [
        'difficulty' => 'easy',
        'is_published' => false,
        'is_vegan' => false,
        'views_count' => 0,
        'rate_likes_count' => 0,
        'rate_medium_count' => 0,
        'rate_dislikes_count' => 0,
        'prep_time' => 0,
        'cook_time' => 0,
        'servings' => 0,
    ];

    protected $appends = [
        'total_time'
    ];

    # Связь с автором
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    # Общее время приготовления
    public function getTotalTimeAttribute(): int {
        return (int) $this->prep_time + (int) $this->cook_time;
    }

    # Полный URL
    public function getFullImageUrlAttribute(): ?string {
        if(!$this->image_path) {
            return null;
        }

        if(Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }

    # Публикация рецепта
    public function publish(): bool {
        return $this->update(
            [
                'is_published' => true,
                'published_at' => now()
            ]);
    }

    # Снятие с публикации
    public function unpublish(): bool {
        return $this->update(
            [
                'is_published' => false,
                'published_at' => null
            ]
        );
    }

    # Вегетарианские рецепты
    public function scopeByVegetarian() {
        return $this->where('is_vegan', true);
    }

    # Увеличение счетчика просмотров
    public function incrementViews(): void {
        $this->increment('views_count');
    }

    # Проверка принадлежности пользователю
    public function belongsToUser($userId): bool {
        return $this->user_id = $userId;
    }

}

