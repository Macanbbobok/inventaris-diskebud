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
        Schema::create('mutasi_barang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('ruangan_asal_id')
                ->constrained('ruangan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('ruangan_tujuan_id')
                ->constrained('ruangan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('tanggal_mutasi');
            $table->text('alasan')->nullable();
            $table->enum('kondisi_sebelum', ['baik', 'rusak_ringan', 'rusak_berat']);
            $table->enum('kondisi_sesudah', ['baik', 'rusak_ringan', 'rusak_berat']);

            $table->foreignId('dilakukan_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('tanggal_mutasi', 'mutasi_tanggal_mutasi_idx');
            $table->index(['barang_id', 'tanggal_mutasi'], 'mutasi_barang_tanggal_idx');
            $table->index(['dilakukan_oleh', 'tanggal_mutasi'], 'mutasi_user_tanggal_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_barang');
    }
};
