@extends('layouts.app')
@section('title', 'Incident #' . $incident->id)

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <!-- Incident Details -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">{{ $incident->emergency_type }} Incident</h5>
                @php $statusClass = match($incident->status) { 'Pending' => 'badge-pending', 'In Progress' => 'badge-in-progress', 'Resolved' => 'badge-resolved', default => 'bg-secondary' }; @endphp
                <span class="badge {{ $statusClass }} px-3 py-2">{{ $incident->status }}</span>
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
                        <p class="fw-bold mb-0">{{ $incident->reporter_name }}</p>
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

        @if(auth()->user()->isAdmin() || auth()->user()->isDispatcher())
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-send me-1" style="color:var(--sg-primary);"></i> Dispatch Team</h6>
                <form action="{{ route('incidents.assign', $incident) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <select name="team_id" class="form-select" required>
                            <option value="">-- Choose Team --</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->team_name }} ({{ $team->team_type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sg w-100"><i class="bi bi-send me-1"></i> Deploy Team</button>
                </form>
            </div>
        </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-outline-sg btn-sm"><i class="bi bi-pencil me-1"></i> Edit Incident</a>
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
    var map = L.map('showMap').setView([{{ $incident->latitude }}, {{ $incident->longitude }}], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    L.marker([{{ $incident->latitude }}, {{ $incident->longitude }}]).addTo(map)
        .bindPopup('<strong>{{ $incident->emergency_type }}</strong><br>{{ $incident->location }}').openPopup();
});
</script>
@endif
@endsection
