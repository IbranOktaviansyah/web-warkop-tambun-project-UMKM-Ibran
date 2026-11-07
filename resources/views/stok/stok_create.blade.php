<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Security-Policy"content="upgrade-insecure-requests">
<title>Tambah Stok - Warkop</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
body { display: flex; min-height: 100vh; background-color: #f7f7f7; }

/* MAIN WRAPPER */
.main-wrapper { flex: 1; margin-left: 220px; transition: margin-left 0.3s ease-in-out; padding: 0; }
@media (max-width: 900px) { .main-wrapper { margin-left: 0; } }

/* MAIN CONTENT */
.main { padding: 25px 20px; }

/* HEADER */
.header {
    background-color: #f9f9f9;
    padding: 20px 15px;
    border-radius: 8px;
    margin-bottom: 25px;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    flex-wrap: wrap;
}
.header h1 {
    font-size: 20px;
    color: #111;
    text-align: left;
    width: 100%;
}

/* Saat layar kecil tetap di tengah */
@media (max-width: 600px) {
    .header { justify-content: center; }
    .header h1 { text-align: center; }
}

/* FORM */
.form-container {
    background-color: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.form-group { margin-bottom: 15px; }
label { display: block; font-size: 14px; font-weight: bold; color: #333; margin-bottom: 6px; }
input, select {
    width: 100%;
    padding: 8px 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}
input:focus, select:focus { border-color: #6b46c1; outline: none; }

.error-message { color: red; font-size: 13px; margin-top: 4px; }
input.error-field, select.error-field { border-color: red; }

.form-footer { text-align: right; margin-top: 20px; }
button {
    background-color: #111;
    color: #fff;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
}
button:hover { background-color: #444; }

.alert { background-color: #ffdddd; border-left: 5px solid crimson; padding: 10px; margin-bottom: 15px; color: #333; }
.success { background-color: #ddffdd; border-left: 5px solid green; padding: 10px; margin-bottom: 15px; color: #333; }

/* RESPONSIVE */
@media (max-width: 600px) {
    .header h1 { font-size: 18px; }
    .form-container { padding: 15px; }
}
</style>
</head>
<body>

@include('layouts.sidebar')

<div class="main-wrapper">
    <div class="main">
        <div class="header">
            <h1>TAMBAH STOK</h1>
        </div>

        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="form-container">
            <div class="form-group">
                <label>Jenis Penambahan</label>
                <select id="jenisTambah">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="baru">Stok Baru</option>
                    <option value="lama">Tambah Stok Lama</option>
                </select>
            </div>

            <!-- FORM STOK BARU -->
            <form id="stokFormBaru" action="{{ route('stok.store.baru') }}" method="POST" style="display:none;">
                @csrf
                <div class="form-group">
                    <label>Nama Barang Baru</label>
                    <input type="text" name="nama_barang" placeholder="Masukkan nama barang baru">
                    <div class="error-message"></div>
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" min="1" placeholder="Masukkan jumlah stok">
                    <div class="error-message"></div>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" min="0" placeholder="Masukkan harga per unit">
                    <div class="error-message"></div>
                </div>
                <div class="form-footer">
                    <button type="submit"><i class="fa-solid fa-plus"></i> Simpan Stok Baru</button>
                </div>
            </form>

            <!-- FORM STOK LAMA -->
            <form id="stokFormLama" action="{{ route('stok.store.lama') }}" method="POST" style="display:none;">
                @csrf
                <div class="form-group">
                    <label>Pilih Barang Lama</label>
                    <select name="existing_stok_id" id="existing_stok_id" onchange="updateHargaDefault()">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($stoks as $stok)
                            <option value="{{ $stok->id_barang }}" data-harga="{{ $stok->harga }}">
                                {{ $stok->nama_barang }} (stok: {{ $stok->jumlah }})
                            </option>
                        @endforeach
                    </select>
                    <div class="error-message"></div>
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" min="1" placeholder="Tambahkan jumlah stok">
                    <div class="error-message"></div>
                </div>
                <div class="form-group">
                    <label>Harga per Unit</label>
                    <input type="number" name="harga" id="hargaLama" min="0" placeholder="Harga per unit">
                    <div class="error-message"></div>
                </div>
                <div class="form-footer">
                    <button type="submit"><i class="fa-solid fa-plus"></i> Tambah Jumlah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const jenisTambah = document.getElementById('jenisTambah');
const formBaru = document.getElementById('stokFormBaru');
const formLama = document.getElementById('stokFormLama');
const stokSelect = document.getElementById('existing_stok_id');
const hargaInput = document.getElementById('hargaLama');

function toggleForm() {
    if (jenisTambah.value === 'baru') {
        formBaru.style.display = 'block';
        formLama.style.display = 'none';
    } else if (jenisTambah.value === 'lama') {
        formBaru.style.display = 'none';
        formLama.style.display = 'block';
        updateHargaDefault();
    } else {
        formBaru.style.display = 'none';
        formLama.style.display = 'none';
    }
}

function updateHargaDefault() {
    const selectedOption = stokSelect.selectedOptions[0];
    if (selectedOption && selectedOption.dataset.harga !== undefined) {
        hargaInput.value = selectedOption.dataset.harga;
    } else {
        hargaInput.value = '';
    }
}

jenisTambah.addEventListener('change', toggleForm);
window.addEventListener('load', toggleForm);

function validateForm(form) {
    let valid = true;
    form.querySelectorAll('.error-message').forEach(div => div.innerText = '');
    form.querySelectorAll('input, select').forEach(input => input.classList.remove('error-field'));

    form.querySelectorAll('input, select').forEach(input => {
        if (input.name && input.value.trim() === '') {
            valid = false;
            input.classList.add('error-field');
            const errDiv = input.closest('.form-group').querySelector('.error-message');
            if (errDiv) errDiv.innerText = 'Kolom ini wajib diisi';
        }
    });
    return valid;
}

formBaru.addEventListener('submit', function(e) {
    if (!validateForm(formBaru)) e.preventDefault();
});
formLama.addEventListener('submit', function(e) {
    if (!validateForm(formLama)) e.preventDefault();
});
</script>
</body>
</html>
