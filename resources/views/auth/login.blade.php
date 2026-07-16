<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SiSardi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg,#1e2a38,#2c3e50); min-height:100vh; display:flex; align-items:center; }
        .card-login { max-width: 400px; margin: auto; border: none; border-radius: 1rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="card card-login shadow p-4">
        <div class="text-center mb-3">
            <h4 class="fw-bold">SiSardi</h4>
            <div class="text-muted small">Sistem Sarpras Digital<br>SMP Negeri 1 Turen</div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>
</div>
</body>
</html>
