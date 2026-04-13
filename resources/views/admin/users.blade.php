@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i>All Users</h4>
    <p class="text-muted mb-0">Manage system user accounts and roles</p>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="fw-bold text-muted">{{ $user->id }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php $roleClass = match($user->role) { 'admin' => 'badge-admin', 'dispatcher' => 'badge-dispatcher', 'responder' => 'badge-responder', 'citizen' => 'badge-citizen', default => 'bg-secondary' }; @endphp
                            <span class="badge {{ $roleClass }} px-3 py-2">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td class="text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-sg dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-person-gear me-1"></i> Role
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li><h6 class="dropdown-header">Change Role</h6></li>
                                        @foreach(['citizen', 'responder', 'dispatcher', 'admin'] as $role)
                                        <li>
                                            <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="role" value="{{ $role }}">
                                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $user->role === $role ? 'active' : '' }}">
                                                    @if($user->role === $role)<i class="bi bi-check-lg"></i>@else<i class="bi bi-circle" style="font-size:0.6rem;"></i>@endif
                                                    {{ ucfirst($role) }}
                                                </button>
                                            </form>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
