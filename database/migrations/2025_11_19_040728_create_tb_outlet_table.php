<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_outlet', function (Blueprint $table) {
            $table->integer('id', true)->unsigned();
            $table->string('nama', 100);
            $table->text('alamat');
            $table->string('tlp', 15);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_outlet');
    }
};