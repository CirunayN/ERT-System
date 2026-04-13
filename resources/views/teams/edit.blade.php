@extends('layouts.app')
@section('title', 'Manage Team: ' . $team->team_name)
@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header py-3" style="background: linear-gradient(135deg, var(--sg-primary), var(--sg-primary-dark)); border: none;">
                <h5 class="text-white fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i> Edit Team</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('teams.update', $team) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Team Name</label>
                        <input type="text" name="team_name" class="form-control" value="{{ $team->team_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Team Type</label>
                        <select name="team_type" class="form-select" required>
                            @foreach(['Fire', 'Medical', 'Police', 'Rescue'] as $t)
                                <option value="{{ $t }}" {{ $team->team_type == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Availability</label>
                        <select name="availability_status" class="form-select">
                            @foreach(['Available', 'Deployed', 'Unavailable'] as $s)
                                <option value="{{ $s }}" {{ $team->availability_status == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
                        <button type="submit" class="btn btn-sg px-4"><i class="bi bi-check-lg me-1"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <!-- Responders List -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-badge me-2"></i>Responders ({{ $team->responders->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Name</th><th>Phone</th><th class="text-end">Action</th></tr></thead>
                        <tbody>
                            @forelse($team->responders as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r->name }}</td>
                                <td class="text-muted">{{ $r->phone_number ?? '—' }}</td>
                                <td class="text-end">
                                    <form action="{{ route('teams.removeResponder', [$team, $r]) }}" method="POST" onsubmit="return confirm('Remove this responder?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-person-dash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-3 text-muted">No responders assigned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Responder -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-plus me-2" style="color:var(--sg-primary);"></i>Add Responder</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('teams.addResponder', $team) }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="name" class="form-control" placeholder="Responder name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="phone_number" class="form-control" placeholder="Phone (optional)">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-sg w-100"><i class="bi bi-plus-lg me-1"></i> Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
