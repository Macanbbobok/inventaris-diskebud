<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BarangTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'BRG-0001',
                'Laptop Inventaris',
                'Ruang Sekretariat',
                'Sekretariat',
                '2024-01-01',
                2024,
                'baik',
                'aktif',
                9500000,
            ],
        ];
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

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');

        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}
