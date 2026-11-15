<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('orders', 'service_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->change();
            });

            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('services', 'service_id')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'service_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable(false)->change();
            });
        }
    }
};
