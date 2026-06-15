<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_log', function (Blueprint $table) {

            $table->id();

            $table->string('nama_file');

            $table->string('tipe_import')
                ->nullable();

            $table->enum('jenis_kib', ['B'])
                ->default('B');

            $table->integer('total_baris')
                ->default(0);

            $table->integer('berhasil')
                ->default(0);

            $table->integer('gagal')
                ->default(0);

            $table->integer('duplikat')
                ->default(0);

            $table->enum('status', ['sukses', 'sebagian', 'gagal'])
                ->default('sukses');

            $table->text('catatan_error')
                ->nullable();

            $table->longText('detail_error')
                ->nullable();

            $table->string('path_file')
                ->nullable();

            $table->string('ip_address')
                ->nullable();

            $table->timestamp('waktu_selesai')
                ->nullable();

            $table->foreignId('diupload_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'created_at'], 'import_status_created_idx');
            $table->index('waktu_selesai', 'import_waktu_selesai_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_log');
    }
};
