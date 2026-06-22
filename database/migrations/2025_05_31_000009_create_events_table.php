<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('event_name');
            $table->string('event_type');
            $table->string('decoration_type')->nullable();
            $table->boolean('lighting_service')->default(false);
            $table->boolean('sound_service')->default(false);
            $table->boolean('dj_service')->default(false);
            $table->boolean('photography_service')->default(false);
            $table->boolean('cake_service')->default(false);
            $table->date('event_date');
            $table->string('event_location')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};