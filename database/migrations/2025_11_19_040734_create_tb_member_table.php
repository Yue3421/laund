<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_member', function (Blueprint $table) {
            $table->id(); // id: int(11) PK
            $table->string('nama', 100);
            $table->text('alamat');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tlp', 15);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_member');
    }
};