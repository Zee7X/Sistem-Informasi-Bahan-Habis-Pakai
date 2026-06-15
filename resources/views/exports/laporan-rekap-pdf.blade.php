<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekapitulasi BHP</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .info { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: right; }
        .footer .signature { margin-top: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rekapitulasi Penggunaan Bahan Habis Pakai</h2>
        <h3>Laboratorium Teknik Komputer</h3>
    </div>

    <div class="info">
        <strong>Periode:</strong> {{ $bulan }} {{ $tahun }}<br>
        <strong>Tanggal Cetak:</strong> {{ $tanggal }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode</th>
                <th>Nama Bahan</th>
                <th width="10%">Awal</th>
                <th width="10%">Masuk</th>
                <th width="10%">Keluar</th>
                <th width="10%">Akhir</th>
                <th width="10%">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $idx => $row)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center">{{ $row['kode_bahan'] }}</td>
                <td>{{ $row['nama_bahan'] }}</td>
                <td class="text-center">{{ (float)$row['stok_awal'] }}</td>
                <td class="text-center">{{ (float)$row['total_masuk'] }}</td>
                <td class="text-center">{{ (float)$row['total_keluar'] }}</td>
                <td class="text-center">{{ (float)$row['stok_akhir'] }}</td>
                <td class="text-center">{{ $row['satuan'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Padang, {{ $tanggal }}</p>
        <p>Laboran Pengelola Lab,</p>
        <div class="signature">
            <strong>( ..................................... )</strong><br>
            NIP. .....................................
        </div>
    </div>
</body>
</html>
