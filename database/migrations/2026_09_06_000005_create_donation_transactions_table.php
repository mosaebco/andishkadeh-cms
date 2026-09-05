<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_transactions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('amount_toman');
            $table->string('status', 30)->default('pending');
            $table->string('gateway_reference', 180)->nullable();
            $table->timestampTz('verified_at')->nullable();
            $table->timestampsTz();

            $table->index(['status', 'created_at']);
            $table->index('gateway_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_transactions');
    }
};
