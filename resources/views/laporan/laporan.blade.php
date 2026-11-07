<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<title>Laporan Keuangan - Warkop</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
body { display: flex; min-height: 100vh; background-color: #f7f7f7; }

/* ===== MAIN CONTENT ===== */
.main { flex: 1; padding: 25px 40px; margin-left: 220px; transition: margin-left 0.3s ease-in-out; }
@media (max-width: 900px) { .main { margin-left: 0; padding: 15px; } }

/* ===== HEADER ===== */
.header {
    background-color: #f9f9f9;
    padding: 20px 25px;
    border-radius: 8px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.header h1 { font-size: 20px; color: #111; flex-shrink: 0; }

/* ===== FORM HEADER ===== */
.header form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}
.header form select,
.header form input[type="date"],
.header form button,
.header form a.btn {
    padding: 6px 12px;
    border-radius: 5px;
    font-size: 14px;
    border: 1px solid #ccc;
    flex-shrink: 0;
}
.header form button,
.header form a.btn {
    border: none;
    color: white;
    cursor: pointer;
    text-decoration: none; /* hilangkan underline untuk link */
}
.btn-primary { background: #111; }
.btn-pdf { background: #e74c3c; }
.btn-excel { background: #2ecc71; }
.btn i { font-size: 14px; }

/* ===== SUMMARY ===== */
.summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}
.summary-card {
    background: #fff;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    text-align: center;
}
.summary-card h4 { font-size: 14px; color: #555; }
.summary-card p { font-size: 18px; font-weight: bold; color: #111; margin-top: 5px; }

/* ===== TABLE ===== */
.table-header, .table-row {
    display: grid;
    grid-template-columns: 1.5fr 2fr 1.5fr 1.5fr 1.5fr 1.5fr;
    align-items: center;
    padding: 10px 15px;
    border-radius: 5px;
    text-align: left;
    gap: 10px;
}
.table-header { background-color: #ddd; font-weight: bold; font-size: 14px; margin-bottom: 10px; }
.table-row { background-color: #fff; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.table-row span { font-size: 14px; color: #333; }

/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
    /* Header */
    .header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .header h1 {
        width: 100%;
        text-align: center;
        margin-bottom: 10px;
    }
    .header form {
        flex-direction: column;
        align-items: center;
        gap: 10px;
        width: 100%;
    }
    .header form select,
    #custom-range input,
    .header form button,
    .header form a.btn {
        width: 90%;
        max-width: 200px;
        text-align: center;
    }
    #custom-range {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
        width: 100%;
    }

    /* Samakan tinggi tombol Tampilkan & Export */
    .header form button.btn-primary,
    .header form a.btn {
        height: 35px; /* tinggi sama untuk semua tombol */
        line-height: 35px;
        padding: 0 12px;
        font-size: 15px;
        text-decoration: none; /* hilangkan underline */
    }

    /* Table */
    .table-header { display: none; }
    .table-row { display: block; padding: 15px; border-radius: 8px; margin-bottom: 12px; }
    .table-row span {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #eee;
        padding: 6px 0;
        font-size: 14px;
    }
    .table-row span:last-child { border-bottom: none; }
    .table-row span::before {
        content: attr(data-label);
        font-weight: bold;
        color: #555;
        margin-right: 10px;
    }
}
</style>
</head>
<body>

{{-- Sidebar --}}
@include('layouts.sidebar')

<div class="main">
    <div class="header">
        <h1>LAPORAN KEUANGAN</h1>

        <form method="GET" action="{{ route('laporan.index') }}">
            <select name="filter" id="filter" onchange="toggleCustomDate(this.value)">
                <option value="harian" {{ $filter == 'harian' ? 'selected' : '' }}>Harian</option>
                <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom</option>
            </select>

            <div id="custom-range" style="display: {{ $filter == 'custom' ? 'flex' : 'none' }}; gap:6px;">
                <input type="date" name="start_date" id="start_date"
                    value="{{ isset($startDate) ? date('Y-m-d', strtotime($startDate)) : '' }}">
                <input type="date" name="end_date" id="end_date"
                    value="{{ isset($endDate) ? date('Y-m-d', strtotime($endDate)) : '' }}">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-filter"></i> Tampilkan
            </button>

            <a href="{{ route('laporan.exportPDF', ['filter'=>$filter,'start_date'=>request('start_date'),'end_date'=>request('end_date')]) }}" class="btn btn-pdf" target="_blank">
                <i class="fa fa-file-pdf"></i> Cetak PDF
            </a>

            <a href="{{ route('laporan.exportExcel', ['filter'=>$filter,'start_date'=>request('start_date'),'end_date'=>request('end_date')]) }}" class="btn btn-excel" target="_blank">
                <i class="fa fa-file-excel"></i> Cetak Excel
            </a>
        </form>
    </div>

    <!-- ===== SUMMARY ===== -->
    <div class="summary">
        <div class="summary-card"><h4>Total Tunai</h4><p>Rp {{ number_format($totalTunai,0,',','.') }}</p></div>
        <div class="summary-card"><h4>Total Transfer</h4><p>Rp {{ number_format($totalTransfer,0,',','.') }}</p></div>
        <div class="summary-card"><h4>Total QRIS</h4><p>Rp {{ number_format($totalQRIS,0,',','.') }}</p></div>
        <div class="summary-card"><h4>Total Pendapatan</h4><p>Rp {{ number_format($totalPendapatan,0,',','.') }}</p></div>
        <div class="summary-card"><h4>Total Pengeluaran</h4><p>Rp {{ number_format($totalPengeluaran,0,',','.') }}</p></div>
        <div class="summary-card"><h4>Laba Bersih</h4><p>Rp {{ number_format($labaBersih,0,',','.') }}</p></div>
    </div>

    <!-- ===== TABLE ===== -->
    <div class="table-wrapper">
        <div class="table-header">
            <div>Tanggal</div>
            <div>Nama Pemesan</div>
            <div>Deskripsi</div>
            <div>Tunai</div>
            <div>Transfer</div>
            <div>QRIS</div>
        </div>

        @foreach($laporan as $row)
            @php
                $tunai = $transfer = $qris = 0;
                if($row->metode_pembayaran === 'Tunai') $tunai = $row->harga_total;
                elseif($row->metode_pembayaran === 'Transfer') $transfer = $row->harga_total;
                elseif($row->metode_pembayaran === 'QRIS') $qris = $row->harga_total;
            @endphp
            <div class="table-row">
                <span data-label="Tanggal">{{ date('d-m-Y H:i', strtotime($row->tanggal_pemesanan)) }}</span>
                <span data-label="Nama Pemesan">{{ $row->nama_pemesan }}</span>
                <span data-label="Deskripsi">Penjualan</span>
                <span data-label="Tunai">Rp {{ $tunai ? number_format($tunai,0,',','.') : '-' }}</span>
                <span data-label="Transfer">Rp {{ $transfer ? number_format($transfer,0,',','.') : '-' }}</span>
                <span data-label="QRIS">Rp {{ $qris ? number_format($qris,0,',','.') : '-' }}</span>
            </div>
        @endforeach

        @if($laporan->isEmpty())
            <p style="text-align:center; color:#777;">Belum ada data laporan.</p>
        @endif
    </div>
</div>

<script>
function toggleCustomDate(value) {
    document.getElementById('custom-range').style.display = (value === 'custom') ? 'flex' : 'none';
}

const startInput = document.getElementById('start_date');
const endInput = document.getElementById('end_date');

if(startInput && endInput){
    startInput.addEventListener('change', function(){
        endInput.min = this.value;
        if(endInput.value < this.value) endInput.value = this.value;
    });
}
</script>

</body>
</html>
