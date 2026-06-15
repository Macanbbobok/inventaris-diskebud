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
        Schema::create('kib_b_peralatan_mesin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('merk_type', 100)->nullable();
            $table->string('ukuran', 100)->nullable();
            $table->string('bahan', 100)->nullable();
            $table->string('no_seri', 100)->nullable();
            $table->text('spesifikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kib_b_peralatan_mesin');
    }
};
