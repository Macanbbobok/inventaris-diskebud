<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LaporanInventaris;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanBarangController extends Controller
{
    public function barang()
    {
        $barangs = Barang::with('bidang', 'ruangan', 'detailKibB')->get();

        $pdf = Pdf::loadView('laporan.barang', [
            'barangs' => $barangs,
        ])->setPaper('a4', 'landscape');

        $fileName = 'laporan/laporan-barang-' . now()->format('Ymd-His') . '.pdf';

        Storage::disk('public')->put($fileName, $pdf->output());

        LaporanInventaris::create([
            'jenis_laporan' => 'Laporan Barang',
            'periode' => 'tahunan',
            'bulan' => now()->month,
            'tahun' => now()->year,
            'file_laporan' => $fileName,
            'dibuat_oleh' => Auth::id(),
        ]);

        return $pdf->download('laporan-barang.pdf');
    }
}
