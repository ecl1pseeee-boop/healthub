<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();


            $table->integer('prep_time')->nullable()->default(0)->comment('Время подготовки в минутах');
            $table->integer('cook_time')->nullable()->default(0)->comment('Время подготовки в минутах');
            $table->integer('total_time')->virtualAs('prep_time + cook_time');
            $table->integer('servings')->nullable()->default(0);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');

            $table->string('image_path')->nullable();

            $table->boolean('is_published')->default(false);
            $table->boolean('is_vegan')->default(false);

            $table->integer('views_count')->default(0);
            $table->integer('rate_likes_count')->default(0);
            $table->integer('rate_medium_count')->default(0);
            $table->integer('rate_dislikes_count')->default(0);

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->timestamp('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
