<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Dashboard - Warkop</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { display: flex; min-height: 100vh; background-color: #f7f7f7; }

        /* ===== MAIN WRAPPER ===== */
        .main-wrapper {
            flex: 1;
            margin-left: 220px;
            transition: margin-left 0.3s ease-in-out;
            padding: 0;
        }

        @media (max-width: 900px) {
            .main-wrapper { margin-left: 0; }
        }

        /* ===== MAIN CONTENT CONTAINER ===== */
        .main {
            padding: 25px 40px;
            max-width: 1300px; /* ✅ batas lebar maksimum */
            margin: 0 auto;   /* ✅ biar konten selalu di tengah */
            width: 100%;
        }

        /* ===== HEADER ===== */
        .header {
            background-color: #f9f9f9;
            padding: 20px 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .header h1 { font-size: 22px; font-weight: bold; color: #111; }
        .header p { font-size: 14px; color: #666; margin-top: 6px; }

        /* ===== ALERT STOK ===== */
        .alert {
            background-color: #ffecec;
            color: #c0392b;
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5b7b1;
            animation: blink 1.5s infinite alternate;
        }

        .alert i { margin-right: 8px; }
        @keyframes blink { 0% { background-color: #ffecec; } 100% { background-color: #ffdede; } }

        /* ===== CARD CONTAINER ===== */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .card-link { text-decoration: none; color: inherit; }

        .card {
            background-color: #fff;
            height: 180px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            padding-top: 25px;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .card i {
            font-size: 40px;
            color: #6b46c1;
            margin-bottom: 15px;
        }

        .card h3 { font-size: 15px; color: #333; margin-bottom: 8px; }
        .card p { font-size: 13px; color: #555; }

        /* ===== CHART ===== */
        .chart-container {
            margin-top: 40px;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        /* Agar chart tidak terlalu lebar di layar besar */
        .chart-container canvas {
            width: 100% !important;
            max-height: 400px;
        }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            margin-top: 25px;
            color: #777;
            font-size: 14px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .main { padding: 20px; }
            .header h1, .header p { text-align: center; }
            .alert ul { margin-left: 15px; }
            .chart-container { padding: 15px; }
        }

        @media (max-width: 500px) {
            .header h1 { font-size: 18px; }
            .card { height: auto; padding: 20px 10px; }
            .card i { font-size: 32px; }
            .footer { font-size: 12px; }
        }
    </style>
</head>
<body>

@include('layouts.sidebar')

<div class="main-wrapper">
    <div class="main">

        <!-- ALERT STOK MENIPIS -->
        @if (!empty($stokMenipis))
            <div class="alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <strong>Stok Menipis!</strong> :
                <ul style="margin-top: 8px; margin-left: 20px;">
                    @foreach ($stokMenipis as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- HEADER -->
        <div class="header">
            <h1>DASHBOARD</h1>
            <p>Selamat datang kembali, <strong>{{ $namaLengkap }}</strong>!</p>
            <p style="margin-top:4px; color:#555;">
                <i class="fa-regular fa-calendar"></i> {{ $tanggalHariIni }}
            </p>
        </div>

        <div style="height: 10px;"></div>

        <!-- KARTU STATISTIK -->
        <div class="cards">

            <a href="{{ route('menu.index') }}" class="card-link">
                <div class="card">
                    <i class="fa-solid fa-utensils"></i>
                    <h3>KELOLA MENU</h3>
                    <p>Total Produk: <strong>{{ $totalMenu ?? 0 }}</strong></p>
                </div>
            </a>

            <a href="{{ route('stok.index') }}" class="card-link">
                <div class="card">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <h3>KELOLA DATA BARANG</h3>
                    <p>Total Stok: <strong>{{ $totalStok ?? 0 }}</strong></p>
                </div>
            </a>

            @if(Auth::check() && Auth::user()->role === 'admin')
                <a href="{{ route('laporan.index') }}" class="card-link">
                    <div class="card">
                        <i class="fa-solid fa-chart-line"></i>
                        <h3>LAPORAN KEUANGAN</h3>
                        <p>Pendapatan Bulan Ini:</p>
                        <p><strong>Rp {{ number_format($totalOmzet ?? 0, 0, ',', '.') }}</strong></p>
                    </div>
                </a>
            @endif

            <a href="{{ route('pemesanan.index') }}" class="card-link">
                <div class="card">
                    <i class="fa-solid fa-receipt"></i>
                    <h3>PENJUALAN HARI INI</h3>
                    <p><strong>{{ $penjualanHariIni ?? 0 }}</strong> Transaksi</p>
                    <p>Pendapatan: <strong>Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</strong></p>
                </div>
            </a>
        </div>

        <!-- GRAFIK PENJUALAN (ADMIN) -->
        @if(Auth::check() && Auth::user()->role === 'admin')
        <div class="chart-container">
            <h3 style="text-align:center; margin-bottom:15px; color:#333;">Grafik Penjualan Minggu Ini</h3>
            <canvas id="chartPenjualan"></canvas>
        </div>
        @endif

        <!-- FOOTER -->
        @if(Auth::check() && Auth::user()->role === 'admin')
        <div class="footer">
            User aktif: <strong>{{ $totalUserAktif ?? 0 }}</strong>
        </div>
        @endif

    </div>
</div>

<!-- SCRIPT CHART -->
<script>
@if(Auth::check() && Auth::user()->role === 'admin')
    const tanggalLabels = {!! json_encode($labelTanggal) !!} || [];
    const dataTransaksi = {!! json_encode($dataTransaksi) !!} || [];

    const formattedLabels = tanggalLabels.map(tgl => {
        const d = new Date(tgl + "T00:00:00");
        if (isNaN(d)) return tgl;
        const hari = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"][d.getDay()];
        const bulan = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"][d.getMonth()];
        const tanggal = d.getDate().toString().padStart(2,'0');
        return `${hari}, ${tanggal} ${bulan}`;
    });

    const ctx = document.getElementById('chartPenjualan').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: formattedLabels,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: dataTransaksi,
                borderColor: '#6b46c1',
                backgroundColor: 'rgba(107,70,193,0.15)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
@endif
</script>
</body>
</html>
