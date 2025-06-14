<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id'); // Primary key kustom
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('payment_id')->nullable(); // Dibuat nullable karena payment bisa dibuat setelah order
            $table->string('status', 50);
            $table->decimal('total_price', 10, 2);
            $table->text('delivery_address')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->text('notes_for_restaurant')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};