<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Kelola Menu - Warkop</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f7f7f7;
        }

        /* ===== KONTEN UTAMA ===== */
        .main {
            flex: 1;
            padding: 25px 40px;
            margin-left: 220px; /* ruang untuk sidebar */
            transition: margin-left 0.3s ease;
        }

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
        }

        .header .actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .header input {
            padding: 7px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            min-width: 140px;
        }

        .header button {
            padding: 7px 14px;
            border: none;
            background-color: #111;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .header button:hover {
            background-color: #444;
        }

        /* ===== DAFTAR MENU ===== */
        .table-header {
            display: grid;
            grid-template-columns: {{ auth()->user()->role === 'admin' ? '1fr 2fr 1fr 1fr 1fr' : '1fr 2fr 1fr 1fr' }};
            background-color: #ddd;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .menu-item {
            display: grid;
            grid-template-columns: {{ auth()->user()->role === 'admin' ? '1fr 2fr 1fr 1fr 1fr' : '1fr 2fr 1fr 1fr' }};
            align-items: center;
            background-color: #fff;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .menu-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            background-color: #e2e2e2;
        }

        .menu-item span {
            font-size: 14px;
            color: #333;
        }

        .menu-item .actions {
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .menu-item .actions i {
            cursor: pointer;
            color: #333;
        }

        .menu-item .actions i:hover {
            color: #000;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }

            .main {
                margin-left: 0;
                padding: 20px;
            }

            .header {
                flex-direction: column;
                align-items: stretch;
            }

            .header h1 {
                text-align: center;
                width: 100%;
            }

            .header .actions {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }

            .header input, .header button {
                width: 100%;
            }

            .table-header {
                display: none;
            }

            .menu-item {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .menu-item img {
                width: 100%;
                height: auto;
                max-height: 200px;
            }

            .menu-item span {
                width: 100%;
                font-size: 15px;
            }

            .menu-item .actions {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>

    <!-- 🔹 PANGGIL SIDEBAR -->
    @include('layouts.sidebar')

    <!-- 🔹 KONTEN UTAMA -->
    <div class="main">
        <div class="header">
            <h1>KELOLA MENU</h1>

            <form action="{{ route('menu.index') }}" method="GET" class="actions">
                <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Cari produk...">
                <button type="submit">Cari</button>
                @if(auth()->user()->role === 'admin')
                    <button type="button" onclick="window.location='{{ route('menu.create') }}'">+ Tambah Menu</button>
                @endif
            </form>
        </div>

        <div class="table-header">
            <div>Gambar</div>
            <div>Nama Produk</div>
            <div>Kategori</div>
            <div>Harga</div>
            @if(auth()->user()->role === 'admin')
                <div>Aksi</div>
            @endif
        </div>

        @forelse($products as $p)
        <div class="menu-item">
            <img src="{{ $p->gambar ? asset('storage/'.$p->gambar) : asset('images/no-image.png') }}" alt="Gambar Produk">
            <span><strong>{{ $p->nama_produk }}</strong></span>
            <span>{{ $p->kategori }}</span>
            <span>Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
            @if(auth()->user()->role === 'admin')
            <div class="actions">
                <a href="{{ route('menu.edit', $p->id_produk) }}"><i class="fa-solid fa-pen"></i></a>
                <a href="javascript:void(0)" onclick="confirmDelete('{{ route('menu.delete', $p->id_produk) }}')">
                    <i class="fa-solid fa-trash"></i>
                </a>
            </div>
            @endif
        </div>
        @empty
            <p style="text-align:center; color:#777;">Tidak ada produk ditemukan.</p>
        @endforelse
    </div>

    <!-- 🔹 SCRIPT SWEETALERT -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        function confirmDelete(url) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data produk ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>

    <!-- 🔹 NOTIFIKASI BERHASIL -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            showConfirmButton: false,
            timer: 1100
        });
    </script>
    @endif

</body>
</html>
