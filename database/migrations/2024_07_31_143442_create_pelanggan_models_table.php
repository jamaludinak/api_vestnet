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
        Schema::create('pelanggan_models', function (Blueprint $table) {
            $table->id('id_pelanggan_base');
            $table->string('id_pelanggan', 10)->unique();
            $table->unsignedBigInteger('id_paket');
            $table->foreign('id_paket')->references('id_paket')->on('jenis_paket_models')->onDelete('cascade');
            $table->date('tanggal_pemasangan');
            $table->decimal('biaya_berlangganan', 12, 2);
            $table->decimal('biaya_pemasangan', 12, 2);
            $table->string('nik', 16);
            $table->text('foto_ktp');
            $table->timestamps();
        });      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan_models');
    }
};
