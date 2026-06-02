<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_designs', function (Blueprint $table) {
            $table->id('gift_design_id');
            $table->foreignId('service_id')
                  ->constrained('services', 'service_id')
                  ->onDelete('cascade');
            $table->string('item_name');
            $table->string('category');
            $table->string('material')->nullable();
            $table->string('size')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('customization_option')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_designs');
    }
};