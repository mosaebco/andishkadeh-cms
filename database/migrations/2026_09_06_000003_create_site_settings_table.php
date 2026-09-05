<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 60)->unique();
            $table->string('title', 180)->nullable();
            $table->longText('body')->nullable();
            $table->string('url', 2048)->nullable();
            $table->jsonb('settings')->default('{}');
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
