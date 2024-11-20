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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan');
            $table->decimal('jumlah_bayar', 12, 2);
            $table->string('snap_token')->nullable();
            $table->string('kode_transaksi')->nullable();
            $table->enum('status_transaksi', [
                'pending',
                'settlement',
                'batal',
                'kadaluarsa',
                'gagal'
            ])->default('pending');
            $table->json('detail_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
