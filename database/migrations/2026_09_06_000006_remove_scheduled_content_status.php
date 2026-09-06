<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['series', 'posts', 'content_items'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            // Preserve the publication date when converting records from the
            // removed status. A future published_at still delays visibility.
            DB::table($table)
                ->where('status', 'scheduled')
                ->update(['status' => 'published']);
        }
    }

    public function down(): void
    {
        // There is no safe reverse operation: published records may have been
        // created after this migration and cannot be distinguished from the
        // converted records.
    }
};
