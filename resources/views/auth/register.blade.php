<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeGuard ERT - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sg-primary: #e67e22; --sg-primary-dark: #d35400; --sg-navy: #1a202c; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, var(--sg-navy) 0%, #2d3748 50%, var(--sg-navy) 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .register-container { width: 100%; max-width: 440px; }
        .register-brand { text-align: center; margin-bottom: 1.5rem; }
        .register-brand img { width: 56px; height: 56px; border-radius: 14px; margin-bottom: 0.75rem; }
        .register-brand h4 { color: #fff; font-weight: 800; margin-bottom: 0.25rem; }
        .register-brand h4 span { color: var(--sg-primary); }
        .register-brand p { color: #a0aec0; font-size: 0.8rem; }
        .register-card { background: #fff; border-radius: 16px; padding: 2.5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .register-card h5 { font-weight: 700; text-align: center; margin-bottom: 1.5rem; }
        .form-label { font-weight: 600; font-size: 0.88rem; color: #4a5568; }
        .input-group-text { background: #f7fafc; border-right: 0; color: #a0aec0; }
        .form-control { border-left: 0; }
        .form-control:focus { box-shadow: none; border-color: var(--sg-primary); }
        .btn-register { background: linear-gradient(135deg, var(--sg-primary), var(--sg-primary-dark)); border: none; padding: 0.7rem; font-weight: 700; font-size: 0.95rem; border-radius: 8px; }
        .btn-register:hover { background: linear-gradient(135deg, var(--sg-primary-dark), #c0392b); }
        .footer-link { text-align: center; margin-top: 1.5rem; }
        .footer-link a { color: #a0aec0; text-decoration: none; font-size: 0.85rem; }
        .footer-link a:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-brand">
            <img src="{{ asset('logo.png') }}" alt="SafeGuard ERT">
            <h4>Safe<span>Guard</span> ERT</h4>
            <p>Emergency Response Team Management System</p>
        </div>
        <div class="register-card">
            <h5>Create Account</h5>
            @if($errors->any())<div class="alert alert-danger py-2 small">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"></i></span><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span><input type="password" name="password" class="form-control" required></div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span><input type="password" name="password_confirmation" class="form-control" required></div>
                </div>
                <button type="submit" class="btn btn-register text-white w-100"><i class="bi bi-person-plus me-1"></i> Register</button>
                <p class="text-center mt-3 small text-muted">Already have an account? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" style="color:var(--sg-primary);">Login here</a></p>
            </form>
        </div>
        <div class="footer-link"><a href="{{ route('home') }}">&larr; Back to Home</a></div>
    </div>
</body>
</html>
