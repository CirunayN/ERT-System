@extends('layouts.app')
@section('title', 'Team Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Response Teams</h4>
        <p class="text-muted mb-0">Manage emergency response teams and assign responders</p>
    </div>
    <a href="{{ route('teams.create') }}" class="btn btn-sg"><i class="bi bi-plus-lg me-1"></i> Create Team</a>
</div>

<div class="row g-4">
    @forelse($teams as $team)
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $team->team_name }}</h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $team->team_type }}</span>
                    </div>
                    <span class="badge {{ $team->availability_status === 'Available' ? 'bg-success' : ($team->availability_status === 'Deployed' ? 'bg-warning text-dark' : 'bg-secondary') }} px-3 py-2">
                        {{ $team->availability_status }}
                    </span>
                </div>
                <div class="d-flex gap-4 mb-3 text-muted">
                    <div><i class="bi bi-people me-1"></i> <strong>{{ $team->members_count }}</strong> Responders</div>
                    <div><i class="bi bi-clipboard-check me-1"></i> <strong>{{ $team->assignments_count }}</strong> Assignments</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-outline-sg flex-fill"><i class="bi bi-pencil me-1"></i> Manage</a>
                    <form action="{{ route('teams.destroy', $team) }}" method="POST" onsubmit="return confirm('Delete this team?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block mb-2"></i>No teams created yet.
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
