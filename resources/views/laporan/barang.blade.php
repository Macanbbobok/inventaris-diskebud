<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang</title>

    <style>
        body{
            font-family: sans-serif;
            font-size: 12px;
        }

        table{
            width:100%;
            border-collapse: collapse;
            margin-top:20px;
        }

        table, th, td{
            border:1px solid black;
        }

        th, td{
            padding:8px;
            text-align:left;
        }

        h2{
            text-align:center;
        }
    </style>
</head>
<body>

<h2>LAPORAN DATA BARANG</h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Bidang</th>
            <th>Ruangan</th>
            <th>Kondisi</th>
            <th>Status</th>
            <th>Tanggal Perolehan</th>
            <th>Tahun</th>
            <th>Harga</th>
        </tr>
    </thead>

    <tbody>
        @foreach($barangs as $barang)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $barang->kode_barang }}</td>
                <td>{{ $barang->nama_barang }}</td>
                <td>{{ $barang->bidang?->nama_bidang }}</td>
                <td>{{ $barang->ruangan?->nama_ruangan }}</td>
                <td>{{ $barang->kondisi }}</td>
                <td>{{ $barang->status }}</td>
                <td>{{ $barang->tanggal_perolehan?->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $barang->tahun_perolehan }}</td>
                <td>
                    Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
