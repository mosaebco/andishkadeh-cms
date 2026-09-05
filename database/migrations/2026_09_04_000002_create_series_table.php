<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestampTz('published_at')->nullable();
            $table->timestampsTz();

            $table->index(['status', 'published_at', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
