<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_items', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 30);
            $table->foreignId('series_id')->nullable()->constrained('series')->nullOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200);
            $table->text('excerpt')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->jsonb('content_blocks')->default('[]');
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestampTz('published_at')->nullable();
            $table->timestampsTz();

            $table->unique(['type', 'slug']);
            $table->index(['type', 'status', 'published_at', 'sort_order']);
            $table->index(['series_id', 'status', 'published_at', 'sort_order']);
        });

        if (! Schema::hasTable('posts')) {
            return;
        }

        DB::table('posts')
            ->orderBy('id')
            ->chunkById(250, function ($posts): void {
                foreach ($posts as $post) {
                    DB::table('content_items')->insert([
                        'id' => $post->id,
                        'type' => 'post',
                        'series_id' => $post->series_id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'excerpt' => $post->excerpt,
                        'cover_image_path' => $post->cover_image_path,
                        'content_blocks' => $post->content_blocks ?: '[]',
                        'status' => $post->status,
                        'sort_order' => $post->sort_order,
                        'published_at' => $post->published_at,
                        'created_at' => $post->created_at,
                        'updated_at' => $post->updated_at,
                    ]);
                }
            });

        // PostgreSQL sequences do not advance when explicit legacy IDs are
        // inserted during the compatibility copy. Reset it before new
        // content is created so the first generated ID cannot collide.
        if (DB::getDriverName() === 'pgsql') {
            $maxId = (int) DB::table('content_items')->max('id');

            if ($maxId > 0) {
                DB::statement("SELECT setval(pg_get_serial_sequence('content_items', 'id'), {$maxId}, true)");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
};
