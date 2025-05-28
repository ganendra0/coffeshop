<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .register-form {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            margin: auto;
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        }
    </style>
</head>
<body>
    <main class="register-form">
        <form method="POST" action="{{ route('register.store') }}"> {{-- Atau route('register') jika method POSTnya sama --}}
            @csrf
            <div class="text-center mb-4">
                <h1 class="h3 mb-3 fw-normal">Buat Akun Baru</h1>
            </div>

            @include('partials.alerts') {{-- Untuk menampilkan error validasi --}}

            <div class="form-floating mb-3">
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nama Lengkap Anda" value="{{ old('name') }}" required autofocus>
                <label for="name">Nama Lengkap</label>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                <label for="email">Alamat Email</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Nomor Telepon (Require)" value="{{ old('phone') }}">
                <label for="phone">Nomor Telepon</label>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password" required>
                <label for="password_confirmation">Konfirmasi Password</label>
                {{-- Error untuk password_confirmation biasanya terkait dengan error 'password' --}}
            </div>

            <div class="form-floating mb-3">
                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Alamat" style="height: 100px">{{ old('address') }}</textarea>
                <label for="address">Alamat</label>
                 @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- email_verified_at, remember_token, created_at, updated_at dihandle oleh sistem/Laravel --}}

            <button class="w-100 btn btn-lg btn-primary" type="submit">Daftar</button>

            <div class="mt-3 text-center">
                <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
            </div>

            <p class="mt-4 mb-3 text-muted text-center">© {{ date('Y') }}</p>
        </form>
    </main>
</body>
</html>