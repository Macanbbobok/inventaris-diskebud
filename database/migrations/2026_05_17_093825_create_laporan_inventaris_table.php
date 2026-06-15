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
        Schema::create('laporan_inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_laporan', 100);
            $table->enum('periode', ['bulanan', 'triwulanan', 'semesteran', 'tahunan']);
            $table->tinyInteger('bulan')->nullable();
            $table->year('tahun');
            $table->string('file_laporan')->nullable();

            $table->foreignId('dibuat_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['periode', 'tahun', 'bulan'], 'laporan_periode_tahun_bulan_idx');
            $table->index(['jenis_laporan', 'tahun'], 'laporan_jenis_tahun_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_inventaris');
    }
};
