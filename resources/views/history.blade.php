<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"content="upgrade-insecure-requests">
    <title>History Stok - Warkop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f7f7f7;
        }

        /* MAIN CONTENT */
        .main {
            flex: 1;
            padding: 25px 40px;
            margin-left: 220px; /* space for sidebar */
            transition: margin-left 0.3s ease-in-out;
        }

        /* HEADER */
        .header {
            background-color: #f9f9f9;
            padding: 20px 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .header h1 { 
            font-size: 20px; 
            color: #111; 
            flex-shrink: 0; 
        }

        .header form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            width: auto;
        }

        .header input {
            padding: 7px 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .header button {
            border: none;
            padding: 7px 14px;
            border-radius: 4px;
            background-color: #111;
            color: #fff;
            font-size: 13px;
            cursor: pointer;
        }

        .header button:hover { background-color: #444; }

        .add-btn {
            padding: 8px 14px;
            background-color: #6b46c1;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .add-btn:hover { background-color: #553c9a; }

        /* TABLE / HISTORY ITEMS */
        .table-header,
        .stok-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 15px;
            padding: 12px 15px;
            border-radius: 6px;
            font-size: 14px;
            align-items: center;
        }

        .table-header {
            background-color: #ddd;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stok-item {
            background-color: #fff;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .stok-item span { font-size: 14px; color: #333; }

        .status-masuk { color: green; font-weight: bold; }
        .status-keluar { color: red; font-weight: bold; }

        /* RESPONSIVE HP */
        @media (max-width: 900px) {
            body { flex-direction: column; }

            .main { margin-left: 0; padding: 15px; }

            .header { flex-direction: column; align-items: stretch; }
            .header h1 { width: 100%; text-align: center; }
            .header form { width: 100%; flex-direction: column; align-items: stretch; }
            .header input,
            .header button,
            .add-btn { width: 100%; }

            .table-header { display: none; }

            .stok-item {
                display: block;
                padding: 15px;
                border-radius: 8px;
            }

            .stok-item span {
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #eee;
                padding: 6px 0;
            }

            .stok-item span:last-child { border-bottom: none; }

            .stok-item span::before {
                content: attr(data-label);
                font-weight: bold;
                color: #555;
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>

    {{-- PANGGIL SIDEBAR --}}
    @include('layouts.sidebar')

    <div class="main">
        <div class="header">
            <h1>HISTORY STOK</h1>
            <form action="{{ route('stok.history') }}" method="GET">
                <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Cari nama barang...">
                <button type="submit">Cari</button>
                <button type="button" class="add-btn" onclick="window.location='{{ route('stok.index') }}'">← Kembali</button>
            </form>
        </div>

        <div class="table-header">
            <div>Nama Barang</div>
            <div>Status</div>
            <div>Jumlah</div>
            <div>Total</div>
            <div>Tanggal</div>
        </div>

        @forelse($movements as $m)
            <div class="stok-item">
                <span data-label="Nama Barang">{{ $m->nama_barang }}</span>
                <span data-label="Status" class="{{ $m->status == 'Masuk' ? 'status-masuk' : 'status-keluar' }}">{{ $m->status }}</span>
                <span data-label="Jumlah">{{ $m->jumlah }}</span>
                <span data-label="Total">Rp {{ number_format($m->harga, 0, ',', '.') }}</span>
                <span data-label="Tanggal">{{ \Carbon\Carbon::parse($m->created_at)->format('d/m/Y H:i') }}</span>
            </div>
        @empty
            <p style="text-align:center; color:#777; margin-top:20px;">Belum ada riwayat stok.</p>
        @endforelse
    </div>

</body>
</html>
