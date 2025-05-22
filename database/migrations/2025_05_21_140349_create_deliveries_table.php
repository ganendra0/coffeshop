<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->bigIncrements('delivery_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('driver_id');
            $table->string('status', 20)->default('assigned');
            $table->timestamp('delivery_time')->nullable();
            $table->timestamps(); // Record creation/update time

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('driver_id')->references('driver_id')->on('drivers')->onDelete('cascade'); // atau set null jika driver bisa dihapus
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};