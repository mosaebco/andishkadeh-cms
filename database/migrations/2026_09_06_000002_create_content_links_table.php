<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('content_item_id')->constrained('content_items')->cascadeOnDelete();
            $table->string('label', 160);
            $table->string('url', 2048);
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestampsTz();

            $table->index(['content_item_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_links');
    }
};
