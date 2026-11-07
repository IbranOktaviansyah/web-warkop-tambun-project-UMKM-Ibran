<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<title>Kelola Staff - Warkop</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
    body { display: flex; min-height: 100vh; background-color: #f7f7f7; }

    /* Main content */
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
    .header .actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .header input {
        padding: 7px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;
    }
    .header button {
        padding: 8px 14px; border-radius: 4px; font-size: 14px;
        border: none; background-color: #111; color: #fff; cursor: pointer;
    }
    .header button:hover { background-color: #444; }

    /* Table / list */
    .table-header, .staff-item {
        display: grid;
        grid-template-columns: 1fr 2fr 1fr 1fr 1fr 1fr;
        align-items: center;
        padding: 10px 15px;
        border-radius: 5px;
        text-align: left;
        gap: 10px;
    }
    .table-header { background-color: #ddd; font-weight: bold; font-size: 14px; margin-bottom: 10px; }
    .staff-item { background-color: #fff; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 12px; border-radius: 8px; }
    .staff-item span { font-size: 14px; color: #333; }
    .staff-item .actions { display: flex; justify-content: flex-start; align-items: center; gap: 10px; }
    .staff-item .actions i { cursor: pointer; color: #333; }
    .staff-item .actions i:hover { color: #000; }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        body { flex-direction: column; }
        .main { margin-left: 0; padding: 15px; }
        .header { flex-direction: column; align-items: stretch; }
        .header h1 { width: 100%; text-align: center; }
        .header .actions { width: 100%; justify-content: center; }
        .header input, .header button { width: 100%; }

        /* Hide desktop table header */
        .table-header { display: none; }

        /* Card style staff item */
        .staff-item {
            display: block;
            padding: 15px;
            border-radius: 8px;
        }

        .staff-item span,
        .staff-item form,
        .staff-item .actions {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding: 8px 0;
            font-size: 14px;
        }
        .staff-item span:last-child,
        .staff-item form:last-child,
        .staff-item .actions:last-child { border-bottom: none; }

        .staff-item span::before,
        .staff-item form::before,
        .staff-item .actions::before {
            font-weight: bold;
            color: #555;
            margin-right: 10px;
        }
        .staff-item span:nth-child(1)::before { content: "ID"; }
        .staff-item span:nth-child(2)::before { content: "Nama Lengkap"; }
        .staff-item span:nth-child(3)::before { content: "Username"; }
        .staff-item span:nth-child(4)::before { content: "Role"; }
        .staff-item form::before { content: "Status"; }
        .staff-item .actions::before { content: "Aksi"; }

        /* Status button full width */
        .staff-item form button {
            width: auto;
            padding: 4px 8px;
            font-size: 12px;
        }

        /* Align action icons to right */
        .staff-item .actions {
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
        <h1>KELOLA STAFF</h1>

        <form action="{{ route('staff.index') }}" method="GET" class="actions">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari staff...">
            <button type="submit">Cari</button>
            <button type="button" onclick="window.location='{{ route('staff.create') }}'">+ Tambah Staff</button>
        </form>
    </div>

    <div class="table-header">
        <div>ID</div>
        <div>Nama Lengkap</div>
        <div>Username</div>
        <div>Role</div>
        <div>Status</div>
        <div>Aksi</div>
    </div>

    @forelse($users as $u)
        <div class="staff-item">
            <span>{{ $u->id_users }}</span>
            <span>{{ $u->nama_lengkap ?? '-' }}</span>
            <span>{{ $u->username }}</span>
            <span>{{ ucfirst($u->role) }}</span>

            {{-- PERBAIKAN: Mengganti $u->_users menjadi $u->id_users --}}
            <form action="{{ route('staff.toggleStatus', $u->id_users) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="
                    background-color: {{ $u->status == 'aktif' ? '#28a745' : '#dc3545' }};
                    color: white; border: none; padding: 4px 10px;
                    border-radius: 4px; cursor: pointer; font-size: 12px;">
                    {{ ucfirst($u->status) }}
                </button>
            </form>

            <div class="actions">
                <a href="{{ route('staff.edit', $u->id_users) }}"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('staff.destroy', $u->id_users) }}" method="POST" class="delete-form" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-delete" style="background:none; border:none; cursor:pointer; color:#c00;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p style="text-align:center; color:#777;">Tidak ada data staff ditemukan.</p>
    @endforelse
</div>

<!-- SWEETALERT SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data staff ini akan dihapus secara permanen!",
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

<!-- Tampilkan pesan sukses (opsional) -->
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