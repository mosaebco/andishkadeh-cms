<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_methods', function (Blueprint $table): void {
            $table->id();
            $table->string('label', 120);
            $table->string('type', 40);
            $table->string('value', 2048);
            $table->string('icon', 80)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestampsTz();

            $table->index(['is_visible', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_methods');
    }
};
