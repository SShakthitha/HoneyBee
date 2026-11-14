<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The earlier migration may already be recorded on installations where
        // its SQLite change operation silently skipped the missing column.
        if (! Schema::hasColumn('orders', 'service_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('service_id')->nullable()->constrained('services', 'service_id')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'service_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('service_id');
            });
        }
    }
};
