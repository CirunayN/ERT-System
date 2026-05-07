<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SafeGuard ERT - @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        :root {
            --sg-primary: #e67e22;
            --sg-primary-dark: #d35400;
            --sg-navy: #1a202c;
            --sg-navy-light: #2d3748;
            --sg-bg: #f4f6f9;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--sg-bg);
            font-size: 0.95rem;
        }

        /* ===== Top Navbar ===== */
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
            transition: all 0.2s;
        }
        .navbar-sg .nav-link:hover { color: #fff !important; background: rgba(255,255,255,0.06); }
        .navbar-sg .nav-link.active { color: #fff !important; background: var(--sg-primary); }
        .navbar-sg .nav-link i { margin-right: 0.3rem; }
        .navbar-sg .user-dropdown {
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .user-avatar-sm {
            width: 32px; height: 32px;
            background: var(--sg-primary);
            border-radius: 50%;
            display: inline-flex;
            align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 0.85rem;
        }

        /* ===== Content ===== */
        .content-area { padding: 1.5rem 2rem; max-width: 1400px; margin: 0 auto; }

        /* ===== Role Badges ===== */
        .badge-admin { background-color: var(--sg-primary); }
        .badge-dispatcher { background-color: #3182ce; }
        .badge-responder { background-color: #38a169; }
        .badge-citizen { background-color: #0dcaf0; color: #000; }

        /* ===== Stat Cards ===== */
        .stat-card {
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card .stat-number { font-size: 2.2rem; font-weight: 800; line-height: 1; }
        .stat-card .stat-label { font-size: 0.85rem; font-weight: 600; opacity: 0.9; }
        .stat-card .stat-icon {
            position: absolute; top: 50%; right: 1.25rem;
            transform: translateY(-50%); font-size: 2.8rem; opacity: 0.25;
        }
        .stat-card.total { background: linear-gradient(135deg, #3182ce, #2b6cb0); }
        .stat-card.pending { background: linear-gradient(135deg, #d69e2e, #b7791f); }
        .stat-card.critical { background: linear-gradient(135deg, #e53e3e, #c53030); }
        .stat-card.resolved { background: linear-gradient(135deg, #38a169, #2f855a); }
        .stat-card.in-progress { background: linear-gradient(135deg, var(--sg-primary), var(--sg-primary-dark)); }

        /* ===== Danger Badges ===== */
        .badge-low { background-color: #38a169; }
        .badge-medium { background-color: #d69e2e; }
        .badge-high { background-color: #dd6b20; }
        .badge-critical { background-color: #e53e3e; }

        /* ===== Status Badges ===== */
        .badge-pending { background-color: #d69e2e; }
        .badge-in-progress { background-color: #3182ce; }
        .badge-en-route { background-color: #0dcaf0; color: #000; }
        .badge-on-scene { background-color: #e67e22; }
        .badge-resolved, .badge-completed { background-color: #38a169; }

        /* ===== Cards ===== */
        .card { border: none; border-radius: 12px; }
        .card-header { border-radius: 12px 12px 0 0 !important; }

        /* ===== Activity Log ===== */
        .activity-item {
            display: flex; gap: 0.75rem; padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .activity-item:last-child { border-bottom: 0; }
        .activity-icon {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 0.85rem;
        }
        .activity-text { font-size: 0.88rem; color: #4a5568; }
        .activity-time { font-size: 0.75rem; color: #a0aec0; }

        /* ===== Buttons ===== */
        .btn-sg { background: var(--sg-primary); border-color: var(--sg-primary); color: #fff; }
        .btn-sg:hover { background: var(--sg-primary-dark); border-color: var(--sg-primary-dark); color: #fff; }
        .btn-outline-sg { border-color: var(--sg-primary); color: var(--sg-primary); }
        .btn-outline-sg:hover { background: var(--sg-primary); color: #fff; }

        /* ===== Map ===== */
        .map-container { border-radius: 12px; overflow: hidden; border: 2px solid #e2e8f0; }

        /* ===== Table ===== */
        .table th { font-size: 0.85rem; font-weight: 700; color: #4a5568; text-transform: uppercase; letter-spacing: 0.03em; }
        .table td { font-size: 0.9rem; vertical-align: middle; }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-sg sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('logo.png') }}" alt="SafeGuard ERT">
                Safe<span>Guard</span> ERT
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <i class="bi bi-list text-white fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-4 me-auto gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('incidents.*') && !request()->routeIs('incidents.history') ? 'active' : '' }}" href="{{ route('incidents.index') }}">
                            <i class="bi bi-exclamation-triangle"></i> Incidents
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('incidents.history') ? 'active' : '' }}" href="{{ route('incidents.history') }}">
                            <i class="bi bi-clock-history"></i> History
                        </a>
                    </li>
                    @if(auth()->user()->isAdmin() || auth()->user()->isDispatcher())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teams.*') ? 'active' : '' }}" href="{{ route('teams.index') }}">
                            <i class="bi bi-people"></i> Teams
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->isAdmin() || auth()->user()->isDispatcher())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('assignments.*') ? 'active' : '' }}" href="{{ route('assignments.index') }}">
                            <i class="bi bi-send"></i> Assignments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}" href="{{ route('analytics') }}">
                            <i class="bi bi-bar-chart-line"></i> Analytics
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                            <i class="bi bi-person-gear"></i> Users
                        </a>
                    </li>
                    @endif
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="nav-link text-white position-relative" data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill fs-5"></i>
                            @if(auth()->user() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            @forelse(auth()->user()->unreadNotifications as $notification)
                            <li>
                                <a class="dropdown-item py-2 border-bottom" href="{{ $notification->data['url'] ?? '#' }}" onclick="event.preventDefault(); document.getElementById('mark-read-{{ $notification->id }}').submit();">
                                    <div class="d-flex gap-2">
                                        <div class="text-sg-primary mt-1"><i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }}"></i></div>
                                        <div>
                                            <div class="fw-semibold" style="font-size: 0.85rem;">{{ $notification->data['title'] }}</div>
                                            <div class="text-muted text-wrap" style="font-size: 0.8rem;">{{ $notification->data['message'] }}</div>
                                            <div class="text-muted mt-1" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </a>
                                <form id="mark-read-{{ $notification->id }}" action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                            @empty
                            <li><span class="dropdown-item text-muted text-center py-3">No new notifications</span></li>
                            @endforelse
                        </ul>
                    </div>

                    <span class="badge rounded-pill badge-{{ auth()->user()->role }} px-3 py-2">{{ ucfirst(auth()->user()->role) }}</span>
                    <div class="dropdown">
                        <a class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle user-dropdown" href="#" data-bs-toggle="dropdown">
                            <div class="user-avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="content-area">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>

    <!-- Global Simulated Call Modal -->
    <div class="modal fade" id="simulatedCallModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 text-white" style="background: var(--sg-navy); border-radius: 20px;">
                <div class="modal-body text-center p-5">
                    <div class="pulsating-circle mx-auto mb-4" style="width: 80px; height: 80px; background: rgba(56, 161, 105, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 0 rgba(56, 161, 105, 0.7); animation: pulse 1.5s infinite;">
                        <div style="width: 60px; height: 60px; background: #38a169; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-telephone-fill fs-2 text-white"></i>
                        </div>
                    </div>
                    <h5 id="callName" class="fw-bold mb-1">Dispatch Center</h5>
                    <p id="callNumber" class="text-white-50 mb-4 small">Connecting...</p>
                    <div id="callStatus" class="badge bg-success mb-4 px-3 py-2 rounded-pill">Ringing</div>
                    <br>
                    <button type="button" class="btn btn-danger rounded-circle p-3" onclick="endFakeCall()" data-bs-dismiss="modal">
                        <i class="bi bi-telephone-x-fill fs-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(56, 161, 105, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(56, 161, 105, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(56, 161, 105, 0); }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    
    <script>
        // Web Audio API Ringing Generator
        let audioCtx, osc, lfo, gainNode;

        function startFakeCall(name, number, isIncoming = false) {
            document.getElementById('callName').innerText = name || 'Unknown Caller';
            document.getElementById('callNumber').innerText = number || '0000-000-0000';
            document.getElementById('callStatus').innerText = isIncoming ? 'Incoming Call...' : 'Ringing...';

            var myModal = new bootstrap.Modal(document.getElementById('simulatedCallModal'));
            myModal.show();

            // Generate Ringing Sound
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            osc = audioCtx.createOscillator();
            lfo = audioCtx.createOscillator();
            gainNode = audioCtx.createGain();

            // Dual tone standard ring
            osc.type = 'sine';
            osc.frequency.setValueAtTime(440, audioCtx.currentTime); // 440 Hz
            lfo.type = 'square';
            lfo.frequency.setValueAtTime(0.5, audioCtx.currentTime); // 2s on/off cycle

            lfo.connect(gainNode.gain);
            osc.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            // Start sounds
            osc.start();
            lfo.start();

            // Auto pickup simulation
            setTimeout(() => {
                if (audioCtx && audioCtx.state === 'running') {
                    document.getElementById('callStatus').innerText = 'Connected 00:01';
                    document.getElementById('callStatus').classList.replace('bg-success', 'bg-primary');
                    stopRinging();
                }
            }, 3500);
        }

        function stopRinging() {
            if (osc) { osc.stop(); osc.disconnect(); }
            if (lfo) { lfo.stop(); lfo.disconnect(); }
            if (audioCtx) { audioCtx.close(); audioCtx = null; }
        }

        function endFakeCall() {
            stopRinging();
            document.getElementById('callStatus').classList.replace('bg-primary', 'bg-success');
        }
    </script>
    @yield('scripts')
</body>
</html>
