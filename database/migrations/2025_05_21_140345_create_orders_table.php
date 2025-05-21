<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->unsignedBigInteger('user_id')->nullable(); // Sesuai definisi FK
            $table->enum('order_type', ['pickup', 'delivery']);
            $table->string('status', 20)->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->string('payment_method', 20);
            $table->text('delivery_address')->nullable();
            // $table->timestamp('created_at')->useCurrent(); // Sesuai skema
            // $table->timestamp('updated_at')->nullable(); // Jika ingin konsisten
            $table->timestamps();


            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};