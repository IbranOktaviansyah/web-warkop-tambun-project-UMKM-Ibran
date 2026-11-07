<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Nota Pemesanan - {{ $pemesanan->id_pemesanan }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px;
            margin: 0;
        }

        .nota {
            background: white;
            border-radius: 10px;
            padding: 25px 35px;
            max-width: 650px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: relative;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
            word-wrap: break-word;
        }

        td, th {
            padding: 8px 5px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background: #f0f0f0;
        }

        .label {
            width: 40%;
            font-weight: bold;
            color: #333;
        }

        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #ddd;
            padding-top: 10px;
        }

        /* === Tombol === */
        .btn-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .btn {
            background-color: #111;
            color: white;
            border: none;
            height: 38px;
            flex: 1;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background-color 0.2s ease;
            text-align: center;
        }

        .btn:hover {
            background-color: #444;
        }

        .btn-print {
            background-color: #2563eb;
        }

        .btn-print:hover {
            background-color: #1d4ed8;
        }

        /* Bungkus tabel agar tetap tabel di HP */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* === Responsif === */
        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .nota {
                padding: 15px;
                max-width: 100%;
            }

            table {
                font-size: 13px;
            }

            th, td {
                padding: 6px 4px;
            }

            /* Tombol sejajar kiri-kanan tapi kotak */
            .btn-container {
                justify-content: space-between;
            }

            .btn {
                flex: 0 0 48%;
                max-width: 35%;
                height: 40px;
                border-radius: 10px;
                font-size: 13px;
            }

            .btn i {
                font-size: 16px;
            }
        }

        /* === Mode Cetak === */
        @media print {
            .btn-container { display: none; }
            body { background: white; padding: 0; margin: 0; }
            .nota { box-shadow: none; border: none; margin: 0; padding: 10px 20px; }
            table, th, td { border: 1px solid #000; }
            th { background: #eaeaea !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="nota">
    <div class="btn-container">
        <a href="{{ route('pemesanan.index') }}" class="btn">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
        <button class="btn btn-print" onclick="window.print()">
            <i class="fa fa-print"></i> Cetak
        </button>
    </div>

    <h2>Nota Pemesanan</h2>

    <table>
        <tr><td class="label">ID Pemesanan</td><td>{{ $pemesanan->id_pemesanan }}</td></tr>
        <tr><td class="label">Nama Pemesan</td><td>{{ $pemesanan->nama_pemesan }}</td></tr>
        <tr><td class="label">Kasir</td><td>{{ $pemesanan->kasir }}</td></tr>
        <tr><td class="label">Tanggal</td><td>{{ date('d-m-Y H:i', strtotime($pemesanan->tanggal_pemesanan)) }}</td></tr>
        <tr><td class="label">Metode Pembayaran</td><td>{{ $pemesanan->metode_pembayaran }}</td></tr>
    </table>

    <hr style="margin: 15px 0;">

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($pemesanan->nama_barang as $i => $item)
                    @php
                        $harga = $pemesanan->harga_satuan[$i] ?? 0;
                        $jumlah = $pemesanan->jumlah_pemesanan[$i] ?? 0;
                        $subtotal = $pemesanan->subtotal[$i] ?? 0;
                        $total += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $item }}</td>
                        <td>Rp {{ number_format($harga, 0, ',', '.') }}</td>
                        <td>{{ $jumlah }}</td>
                        <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="total">Total: Rp {{ number_format($total, 0, ',', '.') }}</p>
</div>

</body>
</html>
