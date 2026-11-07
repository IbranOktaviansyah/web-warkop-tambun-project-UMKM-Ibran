<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Kelola Resep Produk - Warkop</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        body { display: flex; min-height: 100vh; background-color: #f7f7f7; }

        /* Main content area */
        .main { flex: 1; padding: 25px 40px; margin-left: 220px; transition: margin-left 0.3s ease-in-out; }

        /* Header */
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
        .header .actions { display: flex; align-items: center; gap: 10px; }
        .header button {
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 14px;
            border: none;
            background-color: #111;
            color: #fff;
            cursor: pointer;
        }
        .header button:hover { background-color: #444; }

        /* Table (desktop) */
        .table-header, .resep-item {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr {{ Auth::user()->role !== 'kasir' ? '0.5fr' : '' }};
            align-items: center;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: left;
            gap: 10px;
        }
        .table-header {
            background-color: #ddd;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .resep-item {
            background-color: #fff;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .resep-item span { font-size: 14px; color: #333; }
        .resep-item .actions {
            display: flex; justify-content: flex-start; align-items: center; gap: 10px;
        }
        .resep-item .actions i { cursor: pointer; color: #333; }
        .resep-item .actions i:hover { color: #000; }

        /* RESPONSIVE MODE */
        @media (max-width: 900px) {
            body { flex-direction: column; }
            .main { margin-left: 0; padding: 15px; }

            .header {
                flex-direction: column;
                align-items: stretch;
            }
            .header h1 {
                width: 100%;
                text-align: center;
            }
            .header .actions {
                width: 100%;
                justify-content: center;
            }
            .header button { width: 100%; }

            /* ubah tabel jadi tampilan card di HP */
            .table-header { display: none; }

            .resep-item {
                display: block;
                padding: 15px;
                border-radius: 8px;
            }

            .resep-item span,
            .resep-item .actions {
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #eee;
                padding: 6px 0;
                font-size: 14px;
            }

            .resep-item span:last-child,
            .resep-item .actions:last-child {
                border-bottom: none;
            }

            .resep-item span::before {
                content: attr(data-label);
                font-weight: bold;
                color: #555;
                margin-right: 10px;
            }

            .resep-item .actions {
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    <div class="main">
        <div class="header">
            <h1>KELOLA RESEP PRODUK</h1>

            {{-- Tombol tambah hanya untuk admin --}}
            @if(Auth::user()->role !== 'kasir')
            <div class="actions">
                <button type="button" onclick="window.location='{{ route('resep.create') }}'">+ Tambah Resep</button>
            </div>
            @endif
        </div>

        <div class="table-header">
            <div>Nama Produk</div>
            <div>Bahan (Stok)</div>
            <div>Jumlah per Porsi</div>
            @if(Auth::user()->role !== 'kasir')
                <div>Aksi</div>
            @endif
        </div>

        @forelse($resep as $r)
            <div class="resep-item">
                <span data-label="Nama Produk">{{ $r->nama_produk }}</span>
                <span data-label="Bahan (Stok)">{{ $r->nama_barang }}</span>
                <span data-label="Jumlah per Porsi">{{ $r->jumlah_per_porsi }}</span>

                @if(Auth::user()->role !== 'kasir')
                <div class="actions" data-label="Aksi">
                    <form action="{{ route('resep.destroy', $r->id_resep) }}" method="POST" class="delete-form" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-delete" style="background:none; border:none; padding:0; cursor:pointer;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
        @empty
            <p style="text-align:center; color:#777;">Belum ada data resep.</p>
        @endforelse
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data resep ini akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

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
