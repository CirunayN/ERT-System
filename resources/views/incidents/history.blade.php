@extends('layouts.app')
@section('title', 'History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-clock-history me-2"></i>History</h4>
        <p class="text-muted mb-0">Archives of resolved and successfully completed incidents</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <form method="GET" action="{{ route('incidents.history') }}" class="d-inline-flex gap-2 me-2">
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" onchange="this.form.submit()" title="Start Date">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" onchange="this.form.submit()" title="End Date">
        </form>
        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Active Incidents</a>
    </div>
</div>

<div class="row g-4">
    @forelse($incidents as $inc)
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card h-100 shadow-sm border-0 border-top border-4 border-success position-relative" style="opacity: 0.9;">
            <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center pt-3 pb-0">
                <span class="badge bg-secondary"><i class="bi bi-archive-fill me-1"></i>Resolved</span>
                <span class="badge badge-completed"><i class="bi bi-check-lg me-1"></i>Completed</span>
            </div>
            
            <div class="card-body">
                <h6 class="fw-bold mb-1"><a href="{{ route('incidents.show', $inc) }}" class="text-decoration-none text-muted">{{ $inc->emergency_type }}</a></h6>
                <div class="small text-muted mb-3"><i class="bi bi-geo-alt-fill text-secondary me-1"></i>{{ Str::limit($inc->location, 40) }}</div>
                
                <div class="small text-muted mb-0"><i class="bi bi-calendar-check me-1"></i>Closed {{ $inc->updated_at->format('M d, Y') }}</div>
            </div>
            
            <div class="card-footer bg-light border-0 pb-3 pt-2">
                <a href="{{ route('incidents.show', $inc) }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-eye"></i> View Report</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 bg-white rounded shadow-sm border">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 fw-bold text-muted">No History Found</h5>
            <p class="text-muted">No tactical missions have been completed yet.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
