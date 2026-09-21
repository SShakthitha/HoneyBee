<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whats_app_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('singleton_key')->unique()->default(true);
            $table->string('order_number', 15)->nullable();
            $table->text('message_template');
            $table->timestamps();
        });

        DB::table('whats_app_settings')->insert([
            'singleton_key' => true,
            'order_number' => '94767158873',
            'message_template' => "Hello HoneyBee Shop,\n\nI would like to place an order.\n\nOrder ID: #{order_id}\n\nCustomer: {customer_name}\n\nItems:\n{items}\n\nTotal: Rs. {total}\n\nThank you.",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_settings');
    }
};
