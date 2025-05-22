<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id'); // Sesuai definisi PK
            $table->string('name', 50);
            $table->string('email', 50)->unique();
            $table->string('phone', 15);
            $table->string('password', 100);
            $table->text('address')->nullable();
            $table->rememberToken(); // Laravel standard, tambahkan jika perlu
            // $table->timestamp('created_at')->useCurrent(); // Sesuai skema
            // $table->timestamp('updated_at')->nullable(); // Laravel standard
            $table->timestamps(); // Lebih umum untuk created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};