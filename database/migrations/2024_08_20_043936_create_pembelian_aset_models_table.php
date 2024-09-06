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
        Schema::create('pembelian_aset_models', function (Blueprint $table) {
            $table->id('id_pembelian_aset');
            $table->unsignedBigInteger('id_proyek');
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek_models')->onDelete('cascade');
            $table->string('nama_aset');
            $table->integer('jumlah');
            $table->tinyInteger('masa_manfaat');
            $table->date('tanggal');
            $table->decimal('total_biaya', 12, 2);
            $table->decimal('biaya_satuan', 12, 2);
            $table->text('bukti_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_aset_models');
    }
};
