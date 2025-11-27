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
        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id(); // id: bigint unsigned PK, auto-increment
            $table->unsignedBigInteger('id_outlet'); // Adjust ke bigInteger biar match
            $table->string('kode_invoice', 100);
            $table->unsignedBigInteger('id_member'); // Adjust ke bigInteger
            $table->dateTime('tgl');
            $table->dateTime('batas_waktu');
            $table->dateTime('tgl_bayar')->nullable();
            $table->integer('biaya_tambahan');
            $table->double('diskon');
            $table->integer('pajak'); // Tetep integer sesuai PDM
            $table->enum('status', ['baru', 'proses', 'selesai', 'diambil']);
            $table->enum('dibayar', ['dibayar', 'belum_dibayar']);
            $table->unsignedBigInteger('id_user'); // Adjust ke bigInteger
            
            // Foreign keys di akhir
            $table->foreign('id_outlet')->references('id')->on('tb_outlet')->onDelete('cascade');
            $table->foreign('id_member')->references('id')->on('tb_member')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
            
            $table->timestamps(); // Buat created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_transaksi');
    }
};