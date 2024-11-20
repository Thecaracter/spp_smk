<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('jenis_pembayaran_id')->constrained('jenis_pembayaran');
            $table->decimal('total_tagihan', 12, 2);
            $table->decimal('total_terbayar', 12, 2)->default(0);
            $table->enum('status', ['belum_bayar', 'cicilan', 'lunas'])->default('belum_bayar');
            $table->date('tanggal_jatuh_tempo');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
