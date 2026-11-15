<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laser_works', function (Blueprint $table) {
            $table->id('laser_id');
            $table->string('product_name');
            $table->string('laser_type');
            $table->string('material_type')->nullable();
            $table->string('product_category')->nullable();
            $table->string('size')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('engraving_text')->nullable();
            $table->text('description')->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laser_works');
    }
};