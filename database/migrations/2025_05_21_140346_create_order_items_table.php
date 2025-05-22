<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('item_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('menu_id');
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->timestamps(); // Umumnya item juga punya created/updated

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('menu_id')->references('menu_id')->on('menus')->onDelete('restrict'); // Restrict agar menu tidak bisa dihapus jika ada order item
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};