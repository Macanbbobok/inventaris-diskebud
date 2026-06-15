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
        Schema::create('request_kerusakan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->date('tanggal_laporan');
            $table->enum('tingkat_kerusakan', ['ringan', 'sedang', 'berat'])->default('ringan');
            $table->text('deskripsi_kerusakan');
            $table->enum('status', ['diajukan', 'diproses', 'selesai', 'ditolak'])->default('diajukan');

            $table->foreignId('dilaporkan_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'tanggal_laporan'], 'request_status_tanggal_idx');
            $table->index(['tingkat_kerusakan', 'status'], 'request_tingkat_status_idx');
        });

        Schema::create('pemeliharaan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('request_kerusakan_id')
                ->nullable()
                ->constrained('request_kerusakan')
                ->nullOnDelete();

            $table->date('tanggal');
            $table->string('jenis_pemeliharaan', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->enum('kondisi_sesudah', ['baik', 'rusak_ringan', 'rusak_berat']);

            $table->foreignId('dilakukan_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('tanggal', 'pemeliharaan_tanggal_idx');
            $table->index(['barang_id', 'tanggal'], 'pemeliharaan_barang_tanggal_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeliharaan');
        Schema::dropIfExists('request_kerusakan');
    }
};
