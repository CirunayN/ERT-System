@extends('layouts.app')
@section('title', 'Incident #' . $incident->id)

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <!-- Mag show ug Incident Details -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    {{ $incident->emergency_type }} Incident
                    @if($incident->is_verified)
                        <i class="bi bi-patch-check-fill text-success ms-1" title="Verified Incident"></i>
                    @endif
                </h5>
                @php $statusClass = match($incident->status) { 'Pending' => 'badge bg-warning text-dark', 'In Progress' => 'badge bg-primary', 'Completed' => 'badge bg-success', 'En Route' => 'badge bg-info text-dark', 'On Scene' => 'badge bg-warning text-dark', default => 'badge bg-secondary' }; @endphp
                <span class="{{ $statusClass }} px-3 py-2">{{ $incident->status }}</span>
            </div>
            <div class="card-body">
                @if($incident->image_path)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $incident->image_path) }}" class="w-100" style="max-height:300px; object-fit:cover; border-radius:10px;">
                </div>
                @endif
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Reporter</label>
                        <p class="fw-bold mb-0">
                            {{ $incident->reporter_name }}
                            @if($incident->contact_number)
                            <button type="button" class="btn btn-sm btn-outline-success ms-2 rounded-pill py-0 px-2" onclick="startFakeCall('{{ $incident->reporter_name }}', '{{ $incident->contact_number }}')">
                                <i class="bi bi-telephone"></i> Call
                            </button>
                            @endif
                        </p>
                        @if($incident->contact_number)<small class="text-muted">{{ $incident->contact_number }}</small>@endif
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Location</label>
                        <p class="fw-bold mb-0">{{ $incident->location }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold">Type</label>
                        <p class="fw-bold mb-0">{{ $incident->emergency_type }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold">Danger Level</label>
                        @php $dangerClass = match($incident->danger_level) { 'Low' => 'badge-low', 'Medium' => 'badge-medium', 'High' => 'badge-high', 'Critical' => 'badge-critical', default => 'bg-secondary' }; @endphp
                        <p class="mb-0"><span class="badge {{ $dangerClass }}">{{ $incident->danger_level }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small fw-semibold">Date Reported</label>
                        <p class="fw-bold mb-0">{{ $incident->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                <label class="text-muted small fw-semibold">Description</label>
                <div class="bg-light rounded p-3 mb-3">{{ $incident->description }}</div>

                @if($incident->latitude && $incident->longitude)
                <label class="text-muted small fw-semibold"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Location on Map</label>
                <div id="showMap" class="map-container" style="height: 250px;"></div>
                @endif
            </div>
        </div>

        <!-- Assignment History -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Assignment History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Team</th><th>Assigned By</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($incident->assignments as $a)
                            <tr>
                                <td class="fw-semibold">{{ $a->team->team_name }}</td>
                                <td>{{ $a->dispatcher->name ?? 'Admin' }}</td>
                                <td class="text-muted">{{ $a->assignment_date->format('M d, Y H:i') }}</td>
                                <td><span class="badge {{ $a->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($a->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">No teams assigned yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Current Dispatch -->
        <div class="card shadow-sm mb-4" style="background: var(--sg-navy); color: #fff;">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-broadcast me-1"></i> Current Dispatch</h6>
                @if($incident->currentAssignment)
                    <div class="mb-2"><small class="text-muted">Active Team</small><p class="fw-bold fs-5 mb-1">{{ $incident->currentAssignment->team->team_name }}</p></div>
                    <div class="mb-2"><small class="text-muted">Dispatched By</small><p class="mb-1">{{ $incident->currentAssignment->dispatcher->name ?? 'Admin' }}</p></div>
                    <div><small class="text-muted">Since</small><p class="mb-0 small">{{ $incident->currentAssignment->assignment_date->format('M d, Y H:i') }}</p></div>
                @else
                    <div class="text-center py-3">
                        <span class="badge bg-danger mb-2">UNASSIGNED</span>
                        <p class="text-muted small mb-0">No team is currently assigned.</p>
                    </div>
                @endif
            </div>
        </div>


        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Quick Actions</h6>
                
                @if(!$incident->is_verified && (auth()->user()->isAdmin() || auth()->user()->isDispatcher()))
                <form action="{{ route('incidents.verify', $incident) }}" method="POST" class="mb-3 border-bottom pb-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-success w-100"><i class="bi bi-shield-check me-1"></i> Verify Legitimacy</button>
                </form>
                @endif
                
                @if(auth()->user()->isResponder() && auth()->user()->team && $incident->emergency_type === auth()->user()->team->team_type)
                    <div class="d-grid gap-2 mb-3 pb-3 border-bottom">
                        @if($incident->status === 'Pending')
                        <form action="{{ route('incidents.updateStatus', $incident) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="In Progress">
                            <button type="submit" class="btn btn-sg w-100"><i class="bi bi-box-arrow-in-right me-1"></i> Accept Incident</button>
                        </form>
                        @endif
                        @if(in_array($incident->status, ['Pending', 'In Progress']))
                        <form action="{{ route('incidents.updateStatus', $incident) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="En Route">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-truck me-1"></i> En Route</button>
                        </form>
                        @endif
                        @if($incident->status === 'En Route')
                        <form action="{{ route('incidents.updateStatus', $incident) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="On Scene">
                            <button type="submit" class="btn btn-warning text-dark w-100"><i class="bi bi-geo-alt-fill me-1"></i> On Scene</button>
                        </form>
                        @endif
                        @if(in_array($incident->status, ['On Scene', 'In Progress', 'En Route']))
                        <form action="{{ route('incidents.updateStatus', $incident) }}" method="POST" onsubmit="return confirm('Confirm completion?')">
                            @csrf <input type="hidden" name="status" value="Completed">
                            <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle-fill me-1"></i> Completed</button>
                        </form>
                        @endif
                    </div>
                @endif

                <div class="d-grid gap-2">
                    @if(auth()->user()->isAdmin() || auth()->user()->isDispatcher() || auth()->user()->isResponder())
                    <a href="{{ route('incidents.manage', $incident) }}" class="btn btn-outline-sg btn-sm"><i class="bi bi-sliders me-1"></i> Manage Incident</a>
                    @else
                    <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-outline-sg btn-sm"><i class="bi bi-pencil me-1"></i> Edit Incident</a>
                    @endif
                    <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($incident->latitude && $incident->longitude)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var davaoBounds = L.latLngBounds([
        [6.8000, 125.1000],
        [7.5000, 125.7000]
    ]);
    var map = L.map('showMap', {
        maxBounds: davaoBounds,
        maxBoundsViscosity: 1.0,
        minZoom: 10
    }).setView([{{ $incident->latitude }}, {{ $incident->longitude }}], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    L.marker([{{ $incident->latitude }}, {{ $incident->longitude }}]).addTo(map)
        .bindPopup('<strong>{{ $incident->emergency_type }}</strong><br>{{ $incident->location }}').openPopup();
});
</script>
@endif
@endsection
