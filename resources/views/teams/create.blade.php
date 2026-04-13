@extends('layouts.app')
@section('title', 'Create Team')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header py-3" style="background: linear-gradient(135deg, var(--sg-primary), var(--sg-primary-dark)); border: none;">
                <h5 class="text-white fw-bold mb-0"><i class="bi bi-people-fill me-2"></i> Create New Team</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('teams.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                        <input type="text" name="team_name" class="form-control" value="{{ old('team_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Team Type <span class="text-danger">*</span></label>
                        <select name="team_type" class="form-select" required>
                            <option value="">Select Type</option>
                            @foreach(['Fire', 'Medical', 'Police', 'Rescue'] as $t)
                                <option value="{{ $t }}" {{ old('team_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Availability</label>
                        <select name="availability_status" class="form-select">
                            @foreach(['Available', 'Deployed', 'Unavailable'] as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Cancel</a>
                        <button type="submit" class="btn btn-sg px-4"><i class="bi bi-check-lg me-1"></i> Create Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
