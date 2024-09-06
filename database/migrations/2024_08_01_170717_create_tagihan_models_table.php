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
        Schema::create('tagihan_models', function (Blueprint $table) {
            $table->id('id_tagihan');
            $table->string('id_pelanggan', 10);
            $table->foreign('id_pelanggan')
                    ->references('id_pelanggan')
                    ->on('pelanggan_models')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('tagihan', 12, 2);
            $table->string('metode_pembayaran');
            $table->text('bukti_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_models');
    }
};
