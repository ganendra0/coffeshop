<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }
    </style>
</head>
<body>
    <main class="login-form">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="text-center mb-4">
                <h1 class="h3 mb-3 fw-normal">Silakan Login</h1>
            </div>

            @include('partials.alerts') {{-- Jika Anda menggunakan partial alerts --}}
            {{-- Atau jika error validasi login spesifik ditampilkan di sini: --}}
            {{-- @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}


            <div class="form-floating mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                <label for="email">Alamat Email</label>
                @error('email') {{-- Error spesifik untuk field email (jika controller mengirimnya) --}}
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

            <div class="form-check text-start my-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Ingat Saya
                </label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>

            {{-- Tambahkan Link Registrasi di Sini --}}
            <div class="mt-3 text-center">
                <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
            </div>
            {{-- Akhir Link Registrasi --}}

            <p class="mt-4 mb-3 text-muted text-center">© {{ date('Y') }}</p>
        </form>
    </main>
</body>
</html>