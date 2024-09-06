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
        Schema::create('proyek_models', function (Blueprint $table) {
            $table->id('id_proyek');
            $table->string('nama_proyek');
            $table->text('url_map');
            $table->text('foto_banner');
            $table->text('deskripsi');
            $table->text('swot');
            $table->text('simulasi_keutungan');
            $table->decimal('min_invest', 12, 2);
            $table->decimal('dana_terkumpul', 12, 2)->default(0);
            $table->decimal('target_invest', 12, 2);
            $table->string('desa');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->float('roi');
            $table->float('bep');
            $table->char('grade');
            $table->char('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek_models');
    }
};
