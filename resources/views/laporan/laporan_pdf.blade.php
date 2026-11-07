<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Warkop</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { text-align: center; margin-bottom: 20px; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        tfoot td { font-weight: bold; }
    </style>
</head>
<body>

    <h1>LAPORAN KEUANGAN WARKOP</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Pemesan</th>
                <th>Deskripsi</th>
                <th>Tunai</th>
                <th>Transfer</th>
                <th>QRIS</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($laporan as $row)
                @php
                    $tunai = $transfer = $qris = 0;
                    if ($row->metode_pembayaran === 'Tunai') $tunai = $row->harga_total;
                    elseif ($row->metode_pembayaran === 'Transfer') $transfer = $row->harga_total;
                    elseif ($row->metode_pembayaran === 'QRIS') $qris = $row->harga_total;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ date('d-m-Y H:i', strtotime($row->tanggal_pemesanan)) }}</td>
                    <td>{{ $row->nama_pemesan }}</td>
                    <td>Penjualan</td>
                    <td>Rp {{ number_format($tunai, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($transfer, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($qris, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">Belum ada data laporan.</td>
                </tr>
            @endforelse
        </tbody>
        @if(!$laporan->isEmpty())
        <tfoot>
            <tr>
                <td colspan="4" align="right">Total Tunai</td>
                <td colspan="3">Rp {{ number_format($totalTunai, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" align="right">Total Transfer</td>
                <td colspan="3">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" align="right">Total QRIS</td>
                <td colspan="3">Rp {{ number_format($totalQRIS, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" align="right">Total Pemasukan</td>
                <td colspan="3">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>
