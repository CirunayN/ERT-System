@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Welcome, {{ auth()->user()->name }}!</h4>
        <p class="text-muted mb-0">SafeGuard ERT — Command Center</p>
    </div>
    <a href="{{ route('incidents.create') }}" class="btn btn-sg"><i class="bi bi-plus-lg me-1"></i> Report Incident</a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card total">
            <div class="stat-label">Total Incidents</div>
            <div class="stat-number">{{ $totalIncidents }}</div>
            <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card pending">
            <div class="stat-label">Pending</div>
            <div class="stat-number">{{ $pendingCount }}</div>
            <div class="stat-icon"><i class="bi bi-clock"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card critical">
            <div class="stat-label">Critical</div>
            <div class="stat-number">{{ $criticalCount }}</div>
            <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card resolved">
            <div class="stat-label">Resolved</div>
            <div class="stat-number">{{ $resolvedCount }}</div>
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
        </div>
    </div>
</div>

<!-- Map + Activity Log Row -->
<div class="row g-4 mb-4">
    <!-- Incident Map -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Incident Map</h6>
            </div>
            <div class="card-body p-0">
                <div id="dashboardMap" class="map-container" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <!-- Activity Log -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-activity me-2" style="color: var(--sg-primary);"></i>Activity Log</h6>
            </div>
            <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
                @forelse($activityLogs as $log)
                <div class="activity-item">
                    <div class="activity-icon bg-{{ $log->color ?? 'primary' }} bg-opacity-10 text-{{ $log->color ?? 'primary' }}">
                        <i class="bi {{ $log->icon ?? 'bi-activity' }}"></i>
                    </div>
                    <div class="flex-1">
                        <div class="activity-text">{{ $log->description }}</div>
                        <div class="activity-time">
                            <i class="bi bi-clock me-1"></i>{{ $log->created_at->diffForHumans() }}
                            @if($log->user)
                                — {{ $log->user->name }}
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No activity yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Incidents -->
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Recent Incidents</h6>
        <a href="{{ route('incidents.index') }}" class="btn btn-sm btn-outline-sg">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>ID</th><th>Reporter</th><th>Type</th><th>Danger</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($recentIncidents as $incident)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('incidents.show', $incident) }}'">
                        <td class="fw-bold text-muted">#{{ $incident->id }}</td>
                        <td class="fw-semibold">{{ $incident->reporter_name }}</td>
                        <td>{{ $incident->emergency_type }}</td>
                        <td><span class="badge badge-{{ strtolower($incident->danger_level) }}">{{ $incident->danger_level }}</span></td>
                        <td><span class="badge badge-{{ strtolower(str_replace(' ', '-', $incident->status)) }}">{{ $incident->status }}</span></td>
                        <td class="text-muted">{{ $incident->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No incidents yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('dashboardMap').setView([14.5995, 120.9842], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var incidents = @json($mapIncidents);
        var bounds = [];

        incidents.forEach(function(inc) {
            if (inc.latitude && inc.longitude) {
                var color = inc.danger_level === 'Critical' ? '#e53e3e' :
                            inc.danger_level === 'High' ? '#dd6b20' :
                            inc.danger_level === 'Medium' ? '#d69e2e' : '#38a169';

                var marker = L.circleMarker([inc.latitude, inc.longitude], {
                    radius: 8, fillColor: color, color: '#fff', weight: 2, fillOpacity: 0.9
                }).addTo(map);

                var imgHtml = inc.image_path ? '<img src="/storage/' + inc.image_path + '" style="width:100%;max-height:120px;object-fit:cover;border-radius:6px;margin-bottom:8px;">' : '';

                marker.bindPopup(
                    '<div style="min-width:200px;">' +
                    imgHtml +
                    '<strong>' + inc.emergency_type + ' Incident</strong><br>' +
                    '<small class="text-muted"><i class="bi bi-person"></i> ' + inc.reporter_name + '</small><br>' +
                    '<small><i class="bi bi-geo-alt"></i> ' + inc.location + '</small><br>' +
                    '<span class="badge" style="background:'+color+';margin-top:4px;">' + inc.danger_level + '</span> ' +
                    '<span class="badge bg-secondary">' + inc.status + '</span><br>' +
                    '<a href="/incidents/' + inc.id + '" class="btn btn-sm btn-outline-primary mt-2 w-100">View Details</a>' +
                    '</div>'
                );

                bounds.push([inc.latitude, inc.longitude]);
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    });
</script>
@endsection
