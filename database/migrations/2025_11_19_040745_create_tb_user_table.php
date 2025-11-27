<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_user', function (Blueprint $table) {
            $table->id(); // id: int(11) PK
            $table->string('nama', 100);
            $table->string('username', 30);
            $table->text('password');
            $table->foreignId('id_outlet')->constrained('tb_outlet')->onDelete('cascade'); // Relasi ke tb_outlet
            $table->enum('role', ['admin', 'kasir', 'owner']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_user');
    }
};