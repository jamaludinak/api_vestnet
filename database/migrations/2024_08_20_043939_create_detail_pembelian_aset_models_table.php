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
        Schema::create('detail_pembelian_aset_models', function (Blueprint $table) {
            $table->id('id_aset');
            $table->unsignedBigInteger('id_pembelian_aset');
            $table->foreign('id_pembelian_aset')->references('id_pembelian_aset')->on('pembelian_aset_models')->onDelete('cascade');
            $table->string('nama_aset');
            $table->date('tanggal_pembelian');
            $table->date('tanggal_update')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian_aset_models');
    }
};
