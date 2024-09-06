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
        Schema::create('kegiatan_models', function (Blueprint $table) {
            $table->id('id_kegiatan');
            $table->unsignedBigInteger('id_proyek');
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek_models')->onDelete('cascade');
            $table->string('nama_kegiatan');
            $table->decimal('total_biaya', 12, 2);
            $table->date('tgl_kegiatan');
            $table->text('foto_kegiatan');
            $table->text('foto_nota');
            $table->string('jenis_kegiatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_models');
    }
};
