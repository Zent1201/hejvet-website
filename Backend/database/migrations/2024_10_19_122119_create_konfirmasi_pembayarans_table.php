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
        Schema::create('konfirmasi_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayarans')->onDelete('cascade');
            $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_rekening');
            $table->timestamp('tgl_bayar');
            $table->string('status_konsultasi')->nullable();
            $table->string('status_service')->nullable();
            $table->foreignId('konsul_id')->nullable()->constrained('konsuls')->onDelete('set null'); // Menambahkan konsul_id
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null'); // Menambahkan service_id
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfirmasi_pembayarans');
    }
};
