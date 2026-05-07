<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SafeGuard ERT — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0d1117;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 30% 30%, rgba(230,126,34,0.08) 0%, transparent 50%),
                        radial-gradient(ellipse at 70% 70%, rgba(229,62,62,0.05) 0%, transparent 50%);
            animation: bgShift 12s ease-in-out infinite alternate;
        }
        @keyframes bgShift {
            0% { transform: translate(-5%, -5%); }
            100% { transform: translate(5%, 5%); }
        }

        /* Grid bg */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: fadeUp 0.5s ease;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-brand img {
            width: 60px; height: 60px; border-radius: 16px;
            margin-bottom: 14px;
            box-shadow: 0 8px 32px rgba(230,126,34,0.35);
        }
        .login-brand h1 {
            font-size: 1.6rem; font-weight: 900; color: #fff; margin-bottom: 6px;
        }
        .login-brand h1 span { color: #e67e22; }
        .login-brand p { color: #4a5568; font-size: 0.8rem; }

        .login-card {
            background: rgba(22,27,42,0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5);
        }
        .login-card h2 {
            font-size: 1.1rem; font-weight: 800; color: #e2e8f0;
            text-align: center; margin-bottom: 24px;
        }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 0.78rem; font-weight: 600;
            color: #94a3b8; margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #4a5568; font-size: 1rem; pointer-events: none;
        }
        .form-input {
            width: 100%; background: #0f1117;
            border: 1px solid rgba(255,255,255,0.1); border-radius: 10px;
            padding: 11px 14px 11px 38px; color: #e2e8f0;
            font-size: 0.875rem; font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            outline: none; border-color: #e67e22;
            box-shadow: 0 0 0 3px rgba(230,126,34,0.15);
        }
        .form-input::placeholder { color: #2d3748; }
        .toggle-pass {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #4a5568;
            cursor: pointer; font-size: 1rem; transition: color 0.2s;
        }
        .toggle-pass:hover { color: #94a3b8; }

        .form-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .remember-check { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .remember-check input { width: 15px; height: 15px; accent-color: #e67e22; cursor: pointer; }
        .remember-check span { font-size: 0.8rem; color: #64748b; }
        .forgot-link { font-size: 0.8rem; color: #64748b; text-decoration: none; transition: color 0.2s; }
        .forgot-link:hover { color: #e67e22; }

        .btn-login {
            width: 100%; background: linear-gradient(135deg, #e67e22, #d35400);
            border: none; border-radius: 10px; padding: 12px;
            color: #fff; font-weight: 700; font-size: 0.9rem;
            cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: 'Inter', sans-serif;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #d35400, #c0392b);
            box-shadow: 0 8px 24px rgba(230,126,34,0.4);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }

        .divider { text-align: center; color: #2d3748; font-size: 0.78rem; margin: 18px 0; position: relative; }
        .divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: rgba(255,255,255,0.06); }
        .divider span { background: rgba(22,27,42,0.9); padding: 0 12px; position: relative; }

        .register-link { text-align: center; font-size: 0.83rem; color: #4a5568; margin-top: 18px; }
        .register-link a { color: #e67e22; text-decoration: none; font-weight: 700; }
        .register-link a:hover { text-decoration: underline; }

        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #2d3748; font-size: 0.8rem; text-decoration: none; transition: color 0.2s; }
        .back-link a:hover { color: #94a3b8; }

        .error-box {
            background: rgba(229,62,62,0.1); border: 1px solid rgba(229,62,62,0.25);
            border-radius: 10px; padding: 12px 14px; margin-bottom: 18px;
            font-size: 0.82rem; color: #fc8181;
        }
        .error-box i { margin-right: 6px; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-brand">
            <img src="{{ asset('logo.png') }}" alt="SafeGuard ERT">
            <h1>Safe<span>Guard</span> ERT</h1>
            <p>Emergency Response Team Management System</p>
        </div>

        <div class="login-card">
            <h2>Welcome Back 👋</h2>

            @if($errors->any())
            <div class="error-box">
                <i class="bi bi-exclamation-triangle-fill"></i>
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope-fill input-icon"></i>
                        <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="you@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password" class="form-input" placeholder="Your password" id="passInput" required>
                        <button type="button" class="toggle-pass" onclick="togglePass()">
                            <i class="bi bi-eye" id="passIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-row">
                    <label class="remember-check">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>

                <div class="register-link">
                    Don't have an account? <a href="{{ route('register') }}">Create one</a>
                </div>
            </form>
        </div>

        <div class="back-link">
            <a href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> Back to Home</a>
        </div>
    </div>

    <script>
        function togglePass() {
            const input = document.getElementById('passInput');
            const icon = document.getElementById('passIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
