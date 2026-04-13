@extends('layouts.app')
@section('title', 'Incident Assignments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Incident Assignments</h4>
        <p class="text-muted mb-0">Assign available response teams to incidents</p>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Assign -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header py-3" style="background: linear-gradient(135deg, var(--sg-primary), var(--sg-primary-dark)); border: none;">
                <h5 class="text-white fw-bold mb-0"><i class="bi bi-send-fill me-2"></i> Quick Dispatch</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('assignments.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Incident</label>
                        <select name="incident_id" class="form-select" required>
                            <option value="">-- Choose Incident --</option>
                            @foreach($incidents as $inc)
                                <option value="{{ $inc->id }}">#{{ $inc->id }} — {{ $inc->emergency_type }} at {{ Str::limit($inc->location, 30) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Team</label>
                        <select name="team_id" class="form-select" required>
                            <option value="">-- Choose Team --</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->team_name }} ({{ $team->team_type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sg w-100"><i class="bi bi-send me-1"></i> Dispatch Team</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Assignment History -->
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>All Assignments</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Incident</th><th>Team</th><th>Dispatcher</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($assignments as $a)
                            <tr>
                                <td><a href="{{ route('incidents.show', $a->incident_id) }}" class="text-decoration-none fw-semibold">#{{ $a->incident_id }}</a></td>
                                <td class="fw-semibold">{{ $a->team->team_name ?? '—' }}</td>
                                <td>{{ $a->dispatcher->name ?? '—' }}</td>
                                <td class="text-muted">{{ $a->assignment_date->format('M d, H:i') }}</td>
                                <td><span class="badge {{ $a->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($a->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No assignments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
