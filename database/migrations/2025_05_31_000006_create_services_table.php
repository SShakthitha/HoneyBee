<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id('service_id');
            $table->foreignId('business_id')
                  ->constrained('businesses', 'business_id')
                  ->onDelete('cascade');
            $table->string('service_name')->nullable();
            $table->string('service_type');
            $table->text('description')->nullable();
            $table->string('availability_status')->default('available');
            $table->decimal('price', 10, 2);
            $table->string('category')->nullable();
            $table->date('served_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};