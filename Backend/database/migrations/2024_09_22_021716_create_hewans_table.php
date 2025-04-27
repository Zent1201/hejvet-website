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
        Schema::create('hewans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_hewan');
            $table->string('jenis_hewan');
            $table->string('spesies');
            $table->enum('jenis_kelamin', ['jantan', 'betina']);
            $table->integer('umur');
            $table->string('foto_hewan')->nullable();
            $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hewans');
    }
};
