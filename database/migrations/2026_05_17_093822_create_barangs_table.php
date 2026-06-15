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
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang', 150);
            $table->enum('jenis_kib', ['B'])->nullable();
            $table->foreignId('bidang_id')->constrained('bidang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangan')->cascadeOnUpdate()->restrictOnDelete();
            $table->year('tahun_perolehan')->nullable();
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->enum('status', ['aktif', 'dihapus', 'dipinjam'])->default('aktif');
            $table->decimal('harga_perolehan', 15, 2)->default(0);
            $table->string('qr_code')->nullable();
            $table->string('foto')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'kondisi'], 'barang_status_kondisi_idx');
            $table->index('tahun_perolehan', 'barang_tahun_perolehan_idx');
            $table->index('deleted_at', 'barang_deleted_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
