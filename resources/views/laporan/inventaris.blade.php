<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body{
            font-family: sans-serif;
            font-size: 12px;
        }

        h2{
            text-align:center;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse: collapse;
            margin-bottom:30px;
        }

        table, th, td{
            border:1px solid black;
        }

        th, td{
            padding:6px;
            text-align:left;
        }
    </style>
</head>
<body>

<h2>
    LAPORAN
    {{
        match ($data['jenis_laporan']) {
            'barang' => 'BARANG',
            'kib_b' => 'KIB B PERALATAN MESIN',
            'mutasi' => 'MUTASI BARANG',
            'pemeliharaan' => 'PEMELIHARAAN',
            'gabungan' => 'GABUNGAN',
            default => strtoupper($data['jenis_laporan']),
        }
    }}
</h2>

<p>
    Periode:
    {{ ucfirst($data['periode']) }}
    -
    Tahun {{ $data['tahun'] }}
</p>

@if($data['jenis_laporan'] === 'barang' || $data['jenis_laporan'] === 'gabungan')

    <h3>Data Barang</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Tanggal Perolehan</th>
                <th>Bidang</th>
                <th>Ruangan</th>
                <th>Kondisi</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($barangs as $barang)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $barang->kode_barang }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td>{{ $barang->tanggal_perolehan?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $barang->bidang?->nama_bidang }}</td>
                    <td>{{ $barang->ruangan?->nama_ruangan }}</td>
                    <td>{{ $barang->kondisi }}</td>
                    <td>{{ $barang->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endif


@if($data['jenis_laporan'] === 'kib_b' || $data['jenis_laporan'] === 'gabungan')

    <h3>Data KIB B Peralatan Mesin</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Tanggal Perolehan</th>
                <th>Merk / Type</th>
                <th>Ukuran</th>
                <th>Bahan</th>
                <th>No Seri</th>
                <th>Spesifikasi</th>
                <th>Bidang</th>
                <th>Ruangan</th>
            </tr>
        </thead>

        <tbody>
            @foreach($kibBPeralatanMesin as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->barang?->kode_barang }}</td>
                    <td>{{ $item->barang?->nama_barang }}</td>
                    <td>{{ $item->barang?->tanggal_perolehan?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $item->merk_type }}</td>
                    <td>{{ $item->ukuran }}</td>
                    <td>{{ $item->bahan }}</td>
                    <td>{{ $item->no_seri }}</td>
                    <td>{{ $item->spesifikasi }}</td>
                    <td>{{ $item->barang?->bidang?->nama_bidang }}</td>
                    <td>{{ $item->barang?->ruangan?->nama_ruangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endif


@if($data['jenis_laporan'] === 'mutasi' || $data['jenis_laporan'] === 'gabungan')

    <h3>Data Mutasi Barang</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Barang</th>
                <th>Ruangan Asal</th>
                <th>Ruangan Tujuan</th>
                <th>Tanggal</th>
            </tr>
        </thead>

        <tbody>
            @foreach($mutasi as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->barang?->kode_barang }}</td>
                    <td>{{ $item->barang?->nama_barang }}</td>
                    <td>{{ $item->ruanganAsal?->nama_ruangan }}</td>
                    <td>{{ $item->ruanganTujuan?->nama_ruangan }}</td>
                    <td>{{ $item->tanggal_mutasi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endif


@if($data['jenis_laporan'] === 'pemeliharaan' || $data['jenis_laporan'] === 'gabungan')

    <h3>Data Pemeliharaan</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Barang</th>
                <th>Jenis</th>
                <th>Biaya</th>
                <th>Tanggal</th>
            </tr>
        </thead>

        <tbody>
            @foreach($pemeliharaan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->barang?->kode_barang }}</td>
                    <td>{{ $item->barang?->nama_barang }}</td>
                    <td>{{ $item->jenis_pemeliharaan }}</td>
                    <td>
                        Rp {{ number_format($item->biaya ?? 0, 0, ',', '.') }}
                    </td>
                    <td>{{ $item->tanggal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endif

</body>
</html>
