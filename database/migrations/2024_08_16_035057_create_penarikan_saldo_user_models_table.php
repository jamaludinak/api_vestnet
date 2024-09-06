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
        Schema::create('penarikan_saldo_user_models', function (Blueprint $table) {
            $table->id('id_penarikan_saldo');
            $table->unsignedBigInteger('id_investasi');
            $table->foreign('id_investasi')->references('id_investasi')->on('investasi_models')->onDelete('cascade');
            $table->date('tanggal_pengajuan');
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal_acc')->nullable();
            $table->text('bukti_transfer')->nullable();
            $table->tinyInteger('is_verified')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penarikan_saldo_user_models');
    }
};
