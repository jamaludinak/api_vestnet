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
        Schema::create('jurnal_investasi_models', function (Blueprint $table) {
            $table->id('id_jurnal');
            $table->unsignedBigInteger('id_proyek');
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek_models')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('saldo_awal', 12, 2)->default(2);
            $table->decimal('kredit', 12, 2)->default(2);
            $table->decimal('debit', 12, 2)->default(2);
            $table->decimal('saldo_akhir', 12, 2)->default(2);
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_investasi_models');
    }
};
