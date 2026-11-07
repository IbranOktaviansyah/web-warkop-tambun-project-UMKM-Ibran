<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"content="upgrade-insecure-requests">
    <title>Edit Menu - Warkop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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

        /* ===== MAIN CONTENT ===== */
        .main {
            flex: 1;
            padding: 25px 40px;
            margin-left: 220px; /* ruang untuk sidebar */
            transition: margin-left 0.3s ease;
        }

        /* Toggle Button */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            background-color: #6b46c1;
            color: #fff;
            border: none;
            font-size: 20px;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            z-index: 1001;
        }

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

        .header h1 {
            font-size: 20px;
            color: #111;
        }

        .header a {
            background-color: #111;
            color: #fff;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 14px;
        }

        .header a:hover {
            background-color: #333;
        }

        /* ===== FORM ===== */
        .form-container {
            background-color: #fff;
            padding: 25px 35px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #6b46c1;
            outline: none;
        }

        .error-message {
            color: red;
            font-size: 13px;
            margin-top: 4px;
            display: none;
        }

        img.preview {
            width: 100px;
            margin-top: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .form-footer {
            text-align: right;
            margin-top: 20px;
        }

        button {
            background-color: #111;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover {
            background-color: #333;
        }

        /* ===== ALERT ===== */
        .alert {
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .alert-success {
            background: #e7ffe7;
            color: #0a660a;
        }

        .alert-error {
            background: #ffe7e7;
            color: #a00;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .menu-toggle {
                display: block;
            }

            .main {
                margin-left: 0;
                padding: 20px;
            }

            .header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .header a {
                width: 100%;
                text-align: center;
            }

            .form-container {
                padding: 20px;
            }

            button {
                width: 100%;
            }

            img.preview {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>

    <!-- 🔹 TOGGLE BUTTON -->
    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>

    <!-- 🔹 SIDEBAR -->
    @include('layouts.sidebar')

    <!-- 🔹 MAIN CONTENT -->
    <div class="main">
        <div class="header">
            <h1>Edit Menu: {{ $product->nama_produk }}</h1>
        </div>

        <div class="form-container">
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">{{ implode(', ', $errors->all()) }}</div>
            @endif

            <form id="menuEditForm" action="{{ route('menu.update', $product->id_produk) }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" value="{{ $product->nama_produk }}" required>
                    <div class="error-message">Nama produk harus diisi</div>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" required>{{ $product->deskripsi }}</textarea>
                    <div class="error-message">Deskripsi harus diisi</div>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Makanan" {{ $product->kategori == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="Minuman" {{ $product->kategori == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                    </select>
                    <div class="error-message">Kategori harus dipilih</div>
                </div>

                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" step="0.01" value="{{ $product->harga }}" required>
                    <div class="error-message">Harga harus diisi</div>
                </div>

                <div class="form-group">
                    <label>Gambar Produk</label>
                    <input type="file" name="gambar" accept="image/*">
                    @if($product->gambar)
                        <img src="{{ asset('storage/'.$product->gambar) }}" class="preview" alt="Gambar Produk">
                    @endif
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="aktif" {{ $product->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $product->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <div class="error-message">Status harus dipilih</div>
                </div>

                <div class="form-footer">
                    <button type="submit"><i class="fa-solid fa-save"></i> Update Menu</button>
                </div>
            </form>
        </div>
    </div>

<script>
    // 🔸 Fungsi toggle sidebar
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('show');
    }

    // 🔸 Validasi manual sebelum submit
    document.getElementById('menuEditForm').addEventListener('submit', function(e) {
        let isValid = true;
        const form = e.target;
        const fields = form.querySelectorAll('input[required], select[required], textarea[required]');

        fields.forEach(field => {
            const errorMsg = field.parentElement.querySelector('.error-message');
            if (!field.value.trim()) {
                field.style.borderColor = 'red';
                if (errorMsg) errorMsg.style.display = 'block';
                isValid = false;
            } else {
                field.style.borderColor = '#ccc';
                if (errorMsg) errorMsg.style.display = 'none';
            }
        });

        if (!isValid) e.preventDefault();
    });
</script>

</body>
</html>
