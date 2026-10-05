<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('bangunan.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-shell">
    <div class="nb-card" style="width:100%;max-width:420px;">
        <div class="nb-panel-header" style="justify-content:center;flex-direction:column;gap:.4rem;padding-top:1.5rem;">
            <img src="{{ asset('bangunan.png') }}" alt="Logo" style="width:64px;height:64px;object-fit:contain;border:3px solid var(--color-border);border-radius:8px;box-shadow:var(--shadow-sm);background:#fff;">
            <h4 class="mb-0 mt-2">TOKO BANGUNAN</h4>
            <p class="text-muted small mb-0">Masuk ke sistem POS &amp; ERP</p>
        </div>
        <div class="nb-panel-body">
            @if ($errors->any())
                <x-alert type="danger" class="mb-3">
                    {{ $errors->first() }}
                </x-alert>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
                </div>
                <div class="mb-3">
                    <x-password label="Password" name="password" required placeholder="password" />
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">Ingat saya</label>
                </div>
                <button type="submit" class="btn btn-deep btn-nb w-100">Masuk</button>
            </form>

            <div class="mt-4 pt-3" style="border-top:2px solid var(--color-gray);">
                <p class="small fw-bold mb-1">Demo Login:</p>
                <p class="small text-muted mb-0">admin@example.com / owner@example.com / cashier@example.com / warehouse@example.com — password: <b>password</b></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
