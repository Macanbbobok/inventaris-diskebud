<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Barang::with('bidang', 'ruangan')
            ->get()
            ->map(function ($barang) {
                return [
                    'Kode Barang' => $barang->kode_barang,
                    'Nama Barang' => $barang->nama_barang,
                    'Ruangan' => $barang->ruangan?->nama_ruangan,
                    'Bidang' => $barang->bidang?->nama_bidang,
                    'Tanggal Perolehan' => $barang->tanggal_perolehan?->format('Y-m-d'),
                    'Tahun Perolehan' => $barang->tahun_perolehan,
                    'Kondisi' => $barang->kondisi,
                    'Status' => $barang->status,
                    'Harga Perolehan' => $barang->harga_perolehan,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Ruangan',
            'Bidang',
            'Tanggal Perolehan',
            'Tahun Perolehan',
            'Kondisi',
            'Status',
            'Harga Perolehan',
        ];
    }
}
