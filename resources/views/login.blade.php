<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"content="upgrade-insecure-requests">
    <title>Halaman Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background-color: #fff;
            text-align: center;
            padding: 10px 0;
        }
        .banner-image {
            max-width: 180px;
            width: 100%;
            height: auto;
            object-fit: contain;
            margin: 0 auto;
        }
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .login-box {
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            width: 300px;
            background-color: #fff;
        }
        .login-box label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #333;
        }
        .login-box input {
            width: 100%;
            box-sizing: border-box; /* biar sejajar dengan .alert */
            padding: 8px;
            margin-bottom: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .login-box button {
            width: 100%;
            box-sizing: border-box;
            padding: 9px;
            background-color: #222;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .login-box button:hover { background-color: #444; }

        .alert {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 13px;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 12px;
            box-sizing: border-box; 
        }
        .alert.error {
            background-color: #ffecec;
            border: 1px solid #f5b5b5;
            color: #c00;
        }
        .alert.success {
            background-color: #e6f9e6;
            border: 1px solid #b7e1b7;
            color: #0a8a0a;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ asset('images/banner.png') }}" alt="Banner" class="banner-image">
    </div>

    <div class="content">
        <h2>LOGIN</h2>
        <div class="login-box">

            {{-- 🟥 Pesan error atau sukses --}}
            @if (session('error'))
                <div class="alert error">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Masukkan username" value="{{ old('username') }}" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Masukkan password" required>

                <button type="submit">Sign In</button>
            </form>
        </div>
    </div>

</body>
</html>
