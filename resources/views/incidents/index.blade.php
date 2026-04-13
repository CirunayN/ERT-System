@extends('layouts.app')
@section('title', 'All Incidents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Incident Reports</h4>
        <p class="text-muted mb-0">Manage and track all emergency incidents</p>
    </div>
    <a href="{{ route('incidents.create') }}" class="btn btn-sg"><i class="bi bi-plus-lg me-1"></i> Report New Incident</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6"><div class="stat-card critical"><div class="stat-label">Critical</div><div class="stat-number">{{ $criticalCount }}</div><div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card pending"><div class="stat-label">Pending</div><div class="stat-number">{{ $pendingCount }}</div><div class="stat-icon"><i class="bi bi-clock"></i></div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card in-progress"><div class="stat-label">In Progress</div><div class="stat-number">{{ $inProgressCount }}</div><div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card resolved"><div class="stat-label">Resolved</div><div class="stat-number">{{ $resolvedCount }}</div><div class="stat-icon"><i class="bi bi-check-circle"></i></div></div></div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr><th>ID</th><th>Reporter</th><th>Location</th><th>Type</th><th>Danger Level</th><th>Status</th><th>Date/Time</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($incidents as $inc)
                    <tr>
                        <td class="fw-bold text-muted">#{{ $inc->id }}</td>
                        <td><div class="fw-semibold">{{ $inc->reporter_name }}</div>@if($inc->contact_number)<small class="text-muted">{{ $inc->contact_number }}</small>@endif</td>
                        <td>{{ Str::limit($inc->location, 25) }}</td>
                        <td>{{ $inc->emergency_type }}</td>
                        <td><span class="badge badge-{{ strtolower($inc->danger_level) }}">{{ $inc->danger_level }}</span></td>
                        <td><span class="badge badge-{{ strtolower(str_replace(' ', '-', $inc->status)) }}">{{ $inc->status }}</span></td>
                        <td class="text-muted">{{ $inc->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('incidents.show', $inc) }}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('incidents.edit', $inc) }}" class="btn btn-sm btn-outline-sg" title="Edit"><i class="bi bi-pencil"></i></a>
                                @if(!auth()->user()->isDispatcher())
                                <form action="{{ route('incidents.destroy', $inc) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this incident?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No incidents found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
