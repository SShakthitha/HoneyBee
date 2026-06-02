<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handles', function (Blueprint $table) {
            $table->id('handles_id');
            $table->foreignId('staff_id')
                  ->constrained('staff', 'staff_id')
                  ->onDelete('cascade');
            $table->foreignId('service_id')
                  ->constrained('services', 'service_id')
                  ->onDelete('cascade');
            $table->date('assigned_date');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('workload')->default(0);
            $table->string('status')->default('pending');
            $table->text('feedback')->nullable();
            $table->text('attribute')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handles');
    }
};