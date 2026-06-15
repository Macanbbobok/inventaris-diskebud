<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table): void {
            $table->date('tanggal_perolehan')
                ->nullable()
                ->after('tahun_perolehan');

            $table->index('tanggal_perolehan', 'barang_tanggal_perolehan_idx');
        });

        DB::table('barang')
            ->whereNotNull('tahun_perolehan')
            ->whereNull('tanggal_perolehan')
            ->orderBy('id')
            ->chunkById(100, function ($barangs): void {
                foreach ($barangs as $barang) {
                    $tahun = (int) $barang->tahun_perolehan;

                    if ($tahun < 1) {
                        continue;
                    }

                    DB::table('barang')
                        ->where('id', $barang->id)
                        ->update([
                            'tanggal_perolehan' => sprintf('%04d-01-01', $tahun),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table): void {
            $table->dropIndex('barang_tanggal_perolehan_idx');
            $table->dropColumn('tanggal_perolehan');
        });
    }
};
