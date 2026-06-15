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
        Schema::create('bidang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bidang', 100)->unique();
            $table->timestamps();
        });

        Schema::create('ruangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruangan', 20)->unique();
            $table->string('nama_ruangan', 100);
            $table->string('lantai', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('nama_ruangan', 'ruangan_nama_idx');
        });

        Schema::create('bidang_ruangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidang_id')
                ->constrained('bidang')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('ruangan_id')
                ->constrained('ruangan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['bidang_id', 'ruangan_id']);
            $table->index(['ruangan_id', 'bidang_id'], 'bidang_ruangan_ruangan_bidang_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidang_ruangan');
        Schema::dropIfExists('ruangan');
        Schema::dropIfExists('bidang');
    }
};
