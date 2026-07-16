<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SiSardi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/css/adminlte.min.css">
    <style>
        body.login-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 50%, #00c6ff 100%);
        }
        .login-box { margin-top: 8vh; }
        .card-outline.card-primary { border-top: 3px solid #6a11cb; }
        .login-logo b { color: #6a11cb; }
    </style>
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo text-center mb-3">
        <b>SiSardi</b>
        <div class="text-white-50 small">Sistem Sarpras Digital &mdash; SMP Negeri 1 Turen</div>
    </div>

    <div class="card card-outline card-primary shadow-lg">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Masuk untuk mengelola sarana &amp; prasarana sekolah</p>

            @if($errors->any())
                <div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    <div class="input-group-text"><i class="bi bi-envelope"></i></div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                </div>
                <div class="row">
                    <div class="col-7">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                    </div>
                    <div class="col-5">
                        <button type="submit" class="btn w-100 text-white" style="background: linear-gradient(135deg,#6a11cb,#2575fc);">Masuk</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
