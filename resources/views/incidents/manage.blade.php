@extends('layouts.app')
@section('title', 'Manage Incident #' . $incident->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Manage Incident #{{ $incident->id }}</h4>
    <div>
        <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-outline-sg"><i class="bi bi-pencil me-1"></i> Edit Incident Details</a>
        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-secondary ms-2"><i class="bi bi-eye me-1"></i> View Incident</a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Teams and Deployment -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-people-fill me-2" style="color:var(--sg-primary);"></i>Team Deployment & Availability</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($teams as $team)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm" style="background-color: var(--sg-light);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">{{ $team->team_name }}</h6>
                                    @if($team->availability_status === 'Available' && $team->assignments->isEmpty())
                                        <span class="badge bg-success">Available</span>
                                    @else
                                        <span class="badge bg-danger">Deployed</span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-2"><i class="bi bi-tag-fill me-1"></i>{{ $team->team_type }}</p>
                                
                                @if($team->assignments->isNotEmpty())
                                    <div class="alert alert-warning py-2 mb-3">
                                        <p class="small mb-1 fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Currently Deployed To:</p>
                                        <ul class="mb-0 small ps-3">
                                            @foreach($team->assignments as $assignment)
                                                <li>Incident #{{ $assignment->incident_id }} - {{ $assignment->incident->emergency_type ?? 'Unknown' }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <p class="small text-danger mb-0"><i class="bi bi-x-circle-fill me-1"></i>Cannot be deployed until current incident is resolved.</p>
                                @else
                                    <p class="small text-success mb-0"><i class="bi bi-check-circle-fill me-1"></i>Ready for deployment.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin() || auth()->user()->isDispatcher())
        <div class="card shadow-sm border-sg">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-send me-1" style="color:var(--sg-primary);"></i> Assign / Dispatch Team</h6>
                <form action="{{ route('incidents.assign', $incident) }}" method="POST">
                    @csrf
                    <div class="row g-2 align-items-center">
                        <div class="col-md-9">
                            <select name="team_id" class="form-select" required>
                                <option value="">-- Select an Available Team --</option>
                                @foreach($teams as $team)
                                    @if($team->availability_status === 'Available' && $team->assignments->isEmpty())
                                        <option value="{{ $team->id }}">{{ $team->team_name }} ({{ $team->team_type }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-sg w-100"><i class="bi bi-send me-1"></i> Deploy</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Status and Logs -->
    <div class="col-lg-4">
        <!-- Current Dispatch Status -->
        <div class="card shadow-sm mb-4" style="background: var(--sg-navy); color: #fff;">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-broadcast me-1"></i> Current Incident Status</h6>
                
                <div class="mb-3 border-bottom border-secondary pb-3">
                    <small class="text-muted d-block">Overall Status</small>
                    @php $statusClass = match($incident->status) { 'Pending' => 'badge bg-warning text-dark', 'In Progress' => 'badge bg-primary', 'Completed' => 'badge bg-success', 'En Route' => 'badge bg-info text-dark', 'On Scene' => 'badge bg-warning text-dark', default => 'badge bg-secondary' }; @endphp
                    <span class="{{ $statusClass }} fs-6 px-3 py-2 mt-1 d-inline-block">{{ $incident->status }}</span>
                </div>

                @if($incident->currentAssignment)
                    <div class="mb-2"><small class="text-muted">Active Team Deployed</small><p class="fw-bold fs-5 mb-1">{{ $incident->currentAssignment->team->team_name }}</p></div>
                    <div class="mb-2"><small class="text-muted">Dispatched By</small><p class="mb-1">{{ $incident->currentAssignment->dispatcher->name ?? 'Admin' }}</p></div>
                    <div><small class="text-muted">Deployed Since</small><p class="mb-0 small">{{ $incident->currentAssignment->assignment_date->format('M d, Y H:i') }}</p></div>
                @else
                    <div class="text-center py-3">
                        <span class="badge bg-danger mb-2">UNASSIGNED</span>
                        <p class="text-muted small mb-0">No team is currently assigned to this incident.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Quick Update</h6>
                
                @if(!$incident->is_verified && (auth()->user()->isAdmin() || auth()->user()->isDispatcher()))
                <form action="{{ route('incidents.verify', $incident) }}" method="POST" class="mb-3 border-bottom pb-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-success w-100"><i class="bi bi-shield-check me-1"></i> Verify Legitimacy</button>
                </form>
                @endif
                
                @if(auth()->user()->isResponder() && auth()->user()->team && $incident->emergency_type === auth()->user()->team->team_type)
                    <div class="d-grid gap-2">
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
                @elseif(auth()->user()->isAdmin() || auth()->user()->isDispatcher())
                    <form action="{{ route('incidents.updateStatus', $incident) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <select name="status" class="form-select">
                                <option value="Pending" {{ $incident->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ $incident->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="En Route" {{ $incident->status == 'En Route' ? 'selected' : '' }}>En Route</option>
                                <option value="On Scene" {{ $incident->status == 'On Scene' ? 'selected' : '' }}>On Scene</option>
                                <option value="Completed" {{ $incident->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            <button class="btn btn-sg" type="submit">Update</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Activity Log Suggestion snippet -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>Deployment History</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($incident->assignments()->latest()->get() as $log)
                    <div class="list-group-item py-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>{{ $log->team->team_name ?? 'Unknown Team' }}</strong>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="small mb-0 text-muted">
                            Status: <span class="badge {{ $log->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($log->status) }}</span><br>
                            Assigned by: {{ $log->dispatcher->name ?? 'Admin' }}
                        </p>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">
                        No deployment history available.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
