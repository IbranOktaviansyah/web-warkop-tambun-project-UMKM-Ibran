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
        @php 
            $no = 1;
            $totalTunai = $totalTransfer = $totalQRIS = 0;
        @endphp
        @foreach($laporan as $row)
            @php
                $tunai = $transfer = $qris = 0;
                if ($row->metode_pembayaran === 'Tunai') $tunai = $row->harga_total;
                elseif ($row->metode_pembayaran === 'Transfer') $transfer = $row->harga_total;
                elseif ($row->metode_pembayaran === 'QRIS') $qris = $row->harga_total;

                $totalTunai += $tunai;
                $totalTransfer += $transfer;
                $totalQRIS += $qris;
            @endphp
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ date('d-m-Y H:i', strtotime($row->tanggal_pemesanan)) }}</td>
                <td>{{ $row->nama_pemesan }}</td>
                <td>Penjualan</td>
                <td>{{ $tunai }}</td>
                <td>{{ $transfer }}</td>
                <td>{{ $qris }}</td>
            </tr>
        @endforeach

        @if($laporan->isEmpty())
            <tr><td colspan="7" style="text-align:center;">Belum ada data laporan.</td></tr>
        @else
            <tr>
                <td colspan="4"><strong>Total Tunai</strong></td>
                <td><strong>{{ $totalTunai }}</strong></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="4"><strong>Total Transfer</strong></td>
                <td><strong>{{ $totalTransfer }}</strong></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="4"><strong>Total QRIS</strong></td>
                <td><strong>{{ $totalQRIS }}</strong></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="4"><strong>Total Pemasukan</strong></td>
                <td><strong>{{ $totalTunai + $totalTransfer + $totalQRIS }}</strong></td>
                <td colspan="2"></td>
            </tr>
        @endif
    </tbody>
</table>
