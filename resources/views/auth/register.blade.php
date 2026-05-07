<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SafeGuard ERT — Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif; background: #0d1117;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        body::before {
            content: ''; position: fixed; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(ellipse at 70% 30%, rgba(230,126,34,0.08) 0%, transparent 50%),
                        radial-gradient(ellipse at 30% 70%, rgba(56,161,105,0.05) 0%, transparent 50%);
            animation: bgShift 14s ease-in-out infinite alternate;
        }
        @keyframes bgShift { 0% { transform: translate(-5%, -5%); } 100% { transform: translate(5%, 5%); } }
        body::after {
            content: ''; position: fixed; inset: 0;
            background-image: linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .register-wrapper {
            position: relative; z-index: 10; width: 100%;
            max-width: 440px; padding: 20px;
            animation: fadeUp 0.5s ease;
        }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .register-brand { text-align: center; margin-bottom: 24px; }
        .register-brand img { width: 56px; height: 56px; border-radius: 16px; margin-bottom: 12px; box-shadow: 0 8px 32px rgba(230,126,34,0.3); }
        .register-brand h1 { font-size: 1.5rem; font-weight: 900; color: #fff; margin-bottom: 5px; }
        .register-brand h1 span { color: #e67e22; }
        .register-brand p { color: #4a5568; font-size: 0.78rem; }
        .register-card {
            background: rgba(22,27,42,0.9); backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08); border-radius: 20px;
            padding: 28px 32px; box-shadow: 0 32px 80px rgba(0,0,0,0.5);
        }
        .register-card h2 { font-size: 1rem; font-weight: 800; color: #e2e8f0; text-align: center; margin-bottom: 22px; }
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #4a5568; font-size: 0.95rem; pointer-events: none; }
        .form-input {
            width: 100%; background: #0f1117; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; padding: 10px 14px 10px 36px; color: #e2e8f0;
            font-size: 0.875rem; font-family: 'Inter', sans-serif; transition: all 0.2s;
        }
        .form-input:focus { outline: none; border-color: #e67e22; box-shadow: 0 0 0 3px rgba(230,126,34,0.15); }
        .form-input::placeholder { color: #2d3748; }
        .btn-register {
            width: 100%; background: linear-gradient(135deg, #e67e22, #d35400);
            border: none; border-radius: 10px; padding: 12px; color: #fff;
            font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: 'Inter', sans-serif; margin-top: 4px;
        }
        .btn-register:hover { background: linear-gradient(135deg, #d35400, #c0392b); box-shadow: 0 8px 24px rgba(230,126,34,0.4); transform: translateY(-1px); }
        .error-box { background: rgba(229,62,62,0.1); border: 1px solid rgba(229,62,62,0.25); border-radius: 10px; padding: 10px 14px; margin-bottom: 16px; font-size: 0.8rem; color: #fc8181; }
        .login-link { text-align: center; font-size: 0.83rem; color: #4a5568; margin-top: 16px; }
        .login-link a { color: #e67e22; text-decoration: none; font-weight: 700; }
        .login-link a:hover { text-decoration: underline; }
        .back-link { text-align: center; margin-top: 18px; }
        .back-link a { color: #2d3748; font-size: 0.78rem; text-decoration: none; transition: color 0.2s; }
        .back-link a:hover { color: #94a3b8; }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="register-brand">
            <img src="{{ asset('logo.png') }}" alt="SafeGuard ERT">
            <h1>Safe<span>Guard</span> ERT</h1>
            <p>Emergency Response Team Management System</p>
        </div>
        <div class="register-card">
            <h2>Create Account ✨</h2>
            @if($errors->any())
            <div class="error-box">
                <i class="bi bi-exclamation-triangle-fill"></i>
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <div class="input-wrap">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="John Doe" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope-fill input-icon"></i>
                        <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="you@example.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <div class="input-wrap">
                        <i class="bi bi-telephone-fill input-icon"></i>
                        <input type="text" name="phone_number" class="form-input" value="{{ old('phone_number') }}" placeholder="09XX-XXX-XXXX">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password" class="form-input" placeholder="Min. 8 characters" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-shield-lock-fill input-icon"></i>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" required>
                    </div>
                </div>
                <button type="submit" class="btn-register">
                    <i class="bi bi-person-plus-fill"></i> Create Account
                </button>
                <div class="login-link">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
            </form>
        </div>
        <div class="back-link"><a href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> Back to Home</a></div>
    </div>
</body>
</html>
