<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gift_designs', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(0);
        });

        Schema::table('laser_works', function (Blueprint $table) {
            $table->string('production_type')->default('custom_made');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(0);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->unsignedInteger('booking_capacity')->default(0);
            $table->unsignedInteger('reserved_slots')->default(0);
            $table->unsignedInteger('booked_slots')->default(0);
            $table->unsignedInteger('low_slots_threshold')->default(0);
        });

        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->unique()->constrained('order_items')->cascadeOnDelete();
            $table->string('item_type', 20);
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('quantity');
            $table->string('status', 20)->default('reserved');
            $table->timestamps();
            $table->index(['item_type', 'item_id', 'status']);
        });

        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('item_type', 20);
            $table->unsignedBigInteger('item_id');
            $table->string('adjustment_type', 20);
            $table->unsignedInteger('quantity_before');
            $table->unsignedInteger('quantity_after');
            $table->integer('quantity_delta');
            $table->string('note', 500)->nullable();
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['item_type', 'item_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
        Schema::dropIfExists('inventory_reservations');

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['booking_capacity', 'reserved_slots', 'booked_slots', 'low_slots_threshold']);
        });

        Schema::table('laser_works', function (Blueprint $table) {
            $table->dropColumn(['production_type', 'stock_quantity', 'reserved_quantity', 'low_stock_threshold']);
        });

        Schema::table('gift_designs', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'reserved_quantity', 'low_stock_threshold']);
        });
    }
};
