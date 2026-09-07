<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_items')) {
            return;
        }

        DB::table('content_items')
            ->where('status', 'published')
            ->whereNull('published_at')
            ->update(['published_at' => now()]);

        if (Schema::hasTable('series')) {
            DB::table('series')
                ->where('status', 'published')
                ->whereNull('published_at')
                ->update(['published_at' => now()]);
        }
    }

    public function down(): void
    {
        // The original values were NULL and cannot be restored safely after
        // this backfill, so this migration intentionally has no rollback.
    }
};
