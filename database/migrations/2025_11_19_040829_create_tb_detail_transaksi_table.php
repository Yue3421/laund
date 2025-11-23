<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_detail_transaksi', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->unsignedInteger('id_transaksi');
            $table->unsignedInteger('id_paket');
            $table->double('qty');
            $table->text('keterangan')->nullable();
            $table->foreign('id_transaksi')->references('id')->on('tb_transaksi')->onDelete('cascade');
            $table->foreign('id_paket')->references('id')->on('tb_paket')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_detail_transaksi');
    }
};
