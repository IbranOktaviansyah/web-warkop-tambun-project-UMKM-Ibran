<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Pemesanan - Warkop</title>
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
            margin-left: 220px; /* ruang sidebar */
            transition: margin-left 0.3s ease-in-out;
        }

        /* HEADER */
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
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }

        .header h1 { font-size: 20px; color: #111; flex-shrink: 0; }

        .header form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
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

        /* TABLE / DAFTAR PEMESANAN */
        .table-header,
        .transaction-row {
            display: grid;
            grid-template-columns: 1.1fr 1.3fr 1.8fr 0.8fr 1.3fr 1.2fr 1fr 1.3fr 0.8fr;
            gap: 15px;
            padding: 12px 15px;
            border-radius: 6px;
            font-size: 14px;
            align-items: center;
            text-align: left;
        }

        .table-header {
            background-color: #ddd;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .transaction-row {
            background-color: #fff;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .transaction-row div { padding: 5px 0; }

        .aksi-btn {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .aksi-btn:hover { background-color: #1d4ed8; }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            body { flex-direction: column; }
            .main { margin-left: 0; padding: 20px; }

            .header { flex-direction: column; align-items: stretch; text-align: center; }
            .header h1 { width: 100%; margin-bottom: 10px; }
            .header form { flex-direction: column; align-items: stretch; width: 100%; }
            .header input, .header button, .add-btn { width: 100%; }

            .table-header { display: none; }

            .transaction-row {
                display: block;
                padding: 15px;
                border-radius: 8px;
            }

            .transaction-row div {
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #eee;
                padding: 6px 0;
            }

            .transaction-row div:last-child { border-bottom: none; }

            .transaction-row div::before {
                content: attr(data-label);
                font-weight: bold;
                color: #555;
                margin-right: 10px;
            }

            .aksi-btn {
                width: auto;
                padding: 6px 14px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

    {{-- === SIDEBAR === --}}
    @include('layouts.sidebar')

    <div class="main">
        <div class="header">
            <h1>DAFTAR PEMESANAN</h1>

            <form action="{{ route('pemesanan.index') }}" method="GET">
                <input type="text" name="q" placeholder="Cari nama pembeli / kasir..." value="{{ request('q') }}">
                <button type="submit">Cari</button>
                <button type="button" class="add-btn" onclick="window.location='{{ route('pemesanan.create') }}'">+ Tambah Transaksi</button>
            </form>
        </div>

        <div class="table-header">
            <div>ID</div>
            <div>Nama Pemesan</div>
            <div>Nama Barang</div>
            <div>Jumlah</div>
            <div>Metode Pembayaran</div>
            <div>Total</div>
            <div>Kasir</div>
            <div>Tanggal</div>
            <div>Aksi</div>
        </div>

        @forelse($pemesanan as $p)
        <div class="transaction-row">
            <div data-label="ID">{{ $p->id_pemesanan }}</div>
            <div data-label="Nama Pemesan">{{ $p->nama_pemesan }}</div>
            <div data-label="Nama Barang">
                @foreach(json_decode($p->nama_barang) as $item)
                    {{ $item }}<br>
                @endforeach
            </div>
            <div data-label="Jumlah">
                @foreach(json_decode($p->jumlah_pemesanan) as $qty)
                    {{ $qty }}<br>
                @endforeach
            </div>
            <div data-label="Metode Pembayaran">{{ $p->metode_pembayaran }}</div>
            <div data-label="Total">Rp {{ number_format($p->harga_total,0,',','.') }}</div>
            <div data-label="Kasir">{{ $p->kasir }}</div>
            <div data-label="Tanggal">{{ date('d-m-Y H:i', strtotime($p->tanggal_pemesanan)) }}</div>
            <div data-label="Aksi">
                <button class="aksi-btn" onclick="window.location='{{ route('pemesanan.show', $p->id_pemesanan) }}'">
                    <i class="fa-solid fa-receipt"></i> Nota
                </button>
            </div>
        </div>
        @empty
            <p style="text-align:center; color:#777; margin-top:20px;">Tidak ada pemesanan ditemukan.</p>
        @endforelse
    </div>
</body>
</html>
