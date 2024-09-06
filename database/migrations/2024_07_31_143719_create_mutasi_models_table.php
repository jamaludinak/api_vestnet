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
        Schema::create('mutasi_models', function (Blueprint $table) {
            $table->unsignedBigInteger('id_investasi');
            $table->foreign('id_investasi')->references('id_investasi')->on('investasi_models')->onDelete('cascade');
            $table->decimal('saldo_awal', 12, 2)->default(0);
            $table->decimal('kredit', 12, 2)->default(0);
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('saldo_akhir', 12, 2)->default(0);
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_models');
    }
};
