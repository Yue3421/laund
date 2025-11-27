<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_detail_transaksi', function (Blueprint $table) {
            $table->id(); // id: int(11) PK
            $table->foreignId('id_transaksi')->constrained('tb_transaksi')->onDelete('cascade'); // Relasi ke tb_transaksi
            $table->foreignId('id_paket')->constrained('tb_paket')->onDelete('cascade'); // Relasi ke tb_paket
            $table->double('qty');
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_detail_transaksi');
    }
};