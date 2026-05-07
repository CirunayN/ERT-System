<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeGuard ERT — Emergency Response Team Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sg-primary: #e67e22; --sg-primary-dark: #d35400; --sg-navy: #1a202c; --sg-bg: #f4f6f9; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--sg-bg); }
        
        .navbar-sg {
            background: var(--sg-navy);
            padding: 0.5rem 0;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        }
        .navbar-sg .navbar-brand {
            color: #fff;
            font-weight: 800;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .navbar-sg .navbar-brand img {
            width: 34px;
            height: 34px;
            border-radius: 8px;
        }
        .navbar-sg .navbar-brand span { color: var(--sg-primary); }
        .navbar-sg .nav-link {
            color: #a0aec0 !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 0.9rem !important;
            border-radius: 6px;
            transition: all 0.2s
        }
        .navbar-sg .nav-link:hover { color: #fff !important; background: rgba(255,255,255,0.06); }
        .navbar-sg .nav-link i { margin-right: 0.3rem; }

        .hero-section { background: linear-gradient(135deg, var(--sg-navy) 0%, #2d3748 60%, var(--sg-navy) 100%); padding: 5rem 0 4rem; color: #fff; }
        .hero-title { font-size: 3rem; font-weight: 800; line-height: 1.15; margin-bottom: 1.25rem; }
        .hero-subtitle { color: #a0aec0; line-height: 1.7; max-width: 480px; }
        .feature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .feature-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 1.5rem; text-align: center; transition: all 0.3s; }
        .feature-card:hover { background: rgba(255,255,255,0.1); transform: translateY(-4px); }
        .feature-card .feature-icon { font-size: 2rem; margin-bottom: 0.75rem; }
        .feature-card h6 { font-weight: 600; font-size: 0.9rem; margin: 0; }
        .feature-card:nth-child(1) .feature-icon { color: #f6ad55; }
        .feature-card:nth-child(2) .feature-icon { color: #63b3ed; }
        .feature-card:nth-child(3) .feature-icon { color: #fc8181; }
        .feature-card:nth-child(4) .feature-icon { color: #68d391; }
        
        .btn-sg { background: var(--sg-primary); border-color: var(--sg-primary); color: #fff; }
        .btn-sg:hover { background: var(--sg-primary-dark); border-color: var(--sg-primary-dark); color: #fff; }
        
        .features-section { padding: 5rem 0; background: #fff; }
        .about-section { padding: 5rem 0; background: var(--sg-bg); }
        .contact-section { padding: 5rem 0; background: #fff; }
        .section-title { font-weight: 800; font-size: 1.75rem; color: #2d3748; }
        
        .contact-card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; padding: 2.5rem 1.5rem; background: var(--sg-bg); transition: transform 0.3s; height: 100%;}
        .contact-card:hover { transform: translateY(-5px); }
        .contact-icon { font-size: 2.5rem; color: var(--sg-primary); margin-bottom: 1rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-sg sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('logo.png') }}" alt="SafeGuard ERT">
                Safe<span>Guard</span> ERT
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <i class="bi bi-list text-white fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-4 me-auto gap-1">
                    <li class="nav-item">
                        <a class="nav-link" href="#features"><i class="bi bi-star"></i> Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about"><i class="bi bi-info-circle"></i> About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact"><i class="bi bi-envelope"></i> Contact</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm px-3">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm px-3">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-sg btn-sm px-3">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="hero-title">Emergency Response<br>Team System</h1>
                    <p class="hero-subtitle">A comprehensive incident management platform designed to help emergency responders track, manage, and resolve critical incidents efficiently.</p>
                    <div class="d-flex gap-3 mt-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-sg px-4 py-2 fw-semibold">Go to Dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-sg px-4 py-2 fw-semibold">Get Started</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light px-4 py-2 fw-semibold">Login</a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-grid">
                        <div class="feature-card"><div class="feature-icon"><i class="bi bi-exclamation-triangle"></i></div><h6>Incident Tracking</h6></div>
                        <div class="feature-card"><div class="feature-icon"><i class="bi bi-broadcast"></i></div><h6>Real-time Updates</h6></div>
                        <div class="feature-card"><div class="feature-icon"><i class="bi bi-people"></i></div><h6>Team Management</h6></div>
                        <div class="feature-card"><div class="feature-icon"><i class="bi bi-graph-up"></i></div><h6>Analytics</h6></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section" id="features">
        <div class="container text-center">
            <h2 class="section-title mb-2">Powerful Features</h2>
            <p class="text-muted mb-5">Everything you need to manage emergency incidents effectively</p>
            <div class="row g-4">
                <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-4 text-center"><div class="mb-3"><i class="bi bi-clipboard-data" style="font-size:2.5rem;color:var(--sg-primary);"></i></div><h5 class="fw-bold">Incident Reporting</h5><p class="text-muted small">Citizens can quickly report emergencies with photos and map locations.</p></div></div>
                <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-4 text-center"><div class="mb-3"><i class="bi bi-people-fill text-primary" style="font-size:2.5rem;"></i></div><h5 class="fw-bold">Team Dispatch</h5><p class="text-muted small">Dispatchers can assign available response teams to incidents in real time.</p></div></div>
                <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-4 text-center"><div class="mb-3"><i class="bi bi-bar-chart-line-fill text-success" style="font-size:2.5rem;"></i></div><h5 class="fw-bold">Reports & Analytics</h5><p class="text-muted small">Generate statistical reports about incidents and system performance.</p></div></div>
            </div>
        </div>
    </section>

    <section class="about-section" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://images.unsplash.com/photo-1582139329536-e7284fece509?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Emergency Response" class="img-fluid rounded-4 shadow-sm" style="object-fit: cover; height: 400px; width: 100%;">
                </div>
                <div class="col-lg-6 ps-lg-5">
                    <h2 class="section-title mb-3">About SafeGuard ERT</h2>
                    <p class="text-muted mb-4" style="line-height: 1.8;">SafeGuard ERT is a centralized communication and dispatch platform tailored for rapid emergency responses. We empower citizens and responders by bridging the communication gap during critical times.</p>
                    <p class="text-muted mb-4" style="line-height: 1.8;">Our system streamlines the process from the moment an incident is reported to the final resolution, ensuring that teams are optimally dispatched and every action is tracked for maximum transparency and safety.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-section" id="contact">
        <div class="container text-center">
            <h2 class="section-title mb-2">Get In Touch</h2>
            <p class="text-muted mb-5">Have questions or need assistance? Reach out to our support team.</p>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <h5 class="fw-bold">Headquarters</h5>
                        <p class="text-muted small mb-0">123 Emergency Lane<br>Cityville, State 12345</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                        <h5 class="fw-bold">Emergency Hotline</h5>
                        <p class="text-muted small mb-0">+1 (800) 911-SAFE<br>Available 24/7</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                        <h5 class="fw-bold">Email Support</h5>
                        <p class="text-muted small mb-0">support@safeguard-ert.com<br>info@safeguard-ert.com</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 text-center" style="background: var(--sg-navy); color: #a0aec0;">
        <p class="mb-1 small">&copy; {{ date('Y') }} SafeGuard ERT — Emergency Response Team Management System</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
