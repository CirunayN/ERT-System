@extends('layouts.app')
@section('title', 'Active Incidents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Active Incident Board</h4>
        <p class="text-muted mb-0">Track all current ongoing emergencies and tactical deployments</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('incidents.history') }}" class="btn btn-outline-secondary"><i class="bi bi-clock-history me-1"></i> History</a>
        <a href="{{ route('incidents.create') }}" class="btn btn-sg"><i class="bi bi-plus-lg me-1"></i> Report Incident</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6"><div class="stat-card critical"><div class="stat-label">Critical</div><div class="stat-number">{{ $criticalCount }}</div><div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card pending"><div class="stat-label">Pending</div><div class="stat-number">{{ $pendingCount }}</div><div class="stat-icon"><i class="bi bi-clock"></i></div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card in-progress"><div class="stat-label">In Progress</div><div class="stat-number">{{ $inProgressCount }}</div><div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div></div></div>
    <div class="col-md-3 col-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #1a202c, #2d3748);">
            <div class="stat-label">Completed</div><div class="stat-number">{{ $resolvedCount }}</div><div class="stat-icon"><i class="bi bi-shield-fill-check"></i></div>
        </div>
    </div>
</div>

<div class="row g-4">
    @forelse($incidents as $inc)
    <div class="col-xl-3 col-lg-4 col-md-6">
        @php 
            $border = match($inc->danger_level) { 'Critical' => 'danger', 'High' => 'warning', 'Medium' => 'info', default => 'success' };
            $statusClass = strtolower(str_replace(' ', '-', $inc->status));
        @endphp
        <div class="card h-100 shadow-sm border-0 border-top border-4 border-{{ $border }} position-relative" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
            @if($inc->image_path)
                <img src="{{ asset('storage/' . $inc->image_path) }}" class="card-img-top" style="height: 140px; object-fit: cover; border-radius: 0;">
                <div class="position-absolute" style="top: 10px; right: 10px;">
                    <span class="badge badge-{{ $statusClass }} shadow-sm">{{ $inc->status }}</span>
                </div>
            @else
                <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center pt-3 pb-0">
                    <span class="badge badge-{{ strtolower($inc->danger_level) }}"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $inc->danger_level }}</span>
                    <span class="badge badge-{{ $statusClass }}">{{ $inc->status }}</span>
                </div>
            @endif
            
            <div class="card-body">
                @if($inc->image_path)
                    <div class="mb-2"><span class="badge badge-{{ strtolower($inc->danger_level) }}"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $inc->danger_level }}</span></div>
                @endif
                <h6 class="fw-bold mb-1"><a href="{{ route('incidents.show', $inc) }}" class="text-decoration-none text-dark">{{ $inc->emergency_type }}</a></h6>
                <div class="small text-muted mb-3"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ Str::limit($inc->location, 40) }}</div>
                
                <div class="d-flex align-items-center mb-3 p-2 rounded bg-light border">
                    <div class="user-avatar-sm me-2 bg-secondary" style="width:24px;height:24px;font-size:0.6rem;">{{ strtoupper(substr($inc->reporter_name, 0, 1)) }}</div>
                    <div class="small fw-semibold text-truncate" style="max-width: 150px;">{{ $inc->reporter_name }}</div>
                </div>
                
                <div class="small text-muted mb-0"><i class="bi bi-clock me-1"></i>{{ $inc->created_at->diffForHumans() }}</div>
            </div>
            
            <div class="card-footer bg-white border-0 pb-3 pt-0">
                <div class="d-grid gap-2">
                    @if(auth()->user()->isResponder() && $inc->status === 'Pending')
                        <form action="{{ route('incidents.updateStatus', $inc) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="In Progress">
                            <button type="submit" class="btn btn-sg w-100 btn-sm"><i class="bi bi-box-arrow-in-right me-1"></i> Accept Mission</button>
                        </form>
                    @endif
                    <div class="d-flex gap-1">
                        <a href="{{ route('incidents.show', $inc) }}" class="btn btn-outline-dark btn-sm flex-fill"><i class="bi bi-eye"></i> View</a>
                        @if(!auth()->user()->isDispatcher() && !auth()->user()->isCitizen())
                            <form action="{{ route('incidents.destroy', $inc) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm delete?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 bg-white rounded shadow-sm border">
            <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
            <h5 class="mt-3 fw-bold text-muted">All Clear</h5>
            <p class="text-muted">No active incidents reported in the system.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
