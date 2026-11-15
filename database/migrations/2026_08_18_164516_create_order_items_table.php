<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Belongs to an order
            $table->foreignId('order_id')
                  ->constrained('orders', 'order_id')
                  ->onDelete('cascade');

            // Product/service information
            $table->string('item_type');
            $table->unsignedBigInteger('item_id')->nullable();

            $table->string('item_name');

            // Price at the time of ordering
            $table->decimal('price', 10, 2);

            // Quantity
            $table->unsignedInteger('quantity')->default(1);

            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};