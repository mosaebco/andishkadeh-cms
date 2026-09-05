<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('series_id')->constrained('series')->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->text('excerpt')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->json('content_blocks')->default('[]');
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestampTz('published_at')->nullable();
            $table->timestampsTz();

            $table->index(['series_id', 'status', 'published_at', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
