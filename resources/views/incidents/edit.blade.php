@extends('layouts.app')
@section('title', 'Edit Incident #' . $incident->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Incident #{{ $incident->id }}
                </h4>
                <p class="text-muted mb-0">{{ $incident->emergency_type }} · Reported {{ $incident->created_at->diffForHumans() }}</p>
            </div>
            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>

        @php $canEditFully = auth()->user()->isAdmin() || $incident->user_id === auth()->id(); @endphp

        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>@foreach($errors->all() as $error)<div class="small">{{ $error }}</div>@endforeach</div>
        </div>
        @endif

        @if(!$canEditFully)
        <div class="alert alert-info mb-4 d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-2"></i> You only have permission to update the incident's Status.
        </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('incidents.update', $incident) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <h6 class="fw-bold mb-3"><i class="bi bi-person-fill text-primary me-2"></i>Reporter Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reporter Name <span class="text-danger">*</span></label>
                            <input type="text" name="reporter_name" class="form-control" value="{{ old('reporter_name', $incident->reporter_name) }}" {{ !$canEditFully ? 'readonly' : '' }} required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $incident->contact_number) }}" {{ !$canEditFully ? 'readonly' : '' }}>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-top pt-4"><i class="bi bi-fire text-danger me-2"></i>Incident Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Incident Type <span class="text-danger">*</span></label>
                            <select name="emergency_type" class="form-select" {{ !$canEditFully ? 'disabled' : '' }} required>
                                @foreach(['Fire', 'Medical', 'Accident', 'Natural Disaster', 'Crime', 'Rescue', 'Other'] as $type)
                                <option value="{{ $type }}" {{ old('emergency_type', $incident->emergency_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            @if(!$canEditFully)<input type="hidden" name="emergency_type" value="{{ $incident->emergency_type }}">@endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Danger Level <span class="text-danger">*</span></label>
                            <select name="danger_level" class="form-select" {{ !$canEditFully ? 'disabled' : '' }} required>
                                @foreach(['Low', 'Medium', 'High', 'Critical'] as $level)
                                <option value="{{ $level }}" {{ old('danger_level', $incident->danger_level) == $level ? 'selected' : '' }}>{{ $level }}</option>
                                @endforeach
                            </select>
                            @if(!$canEditFully)<input type="hidden" name="danger_level" value="{{ $incident->danger_level }}">@endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                @foreach(['Pending', 'In Progress', 'En Route', 'On Scene', 'Completed'] as $status)
                                <option value="{{ $status }}" {{ old('status', $incident->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Incident Date / Time</label>
                            <input type="datetime-local" name="incident_date" class="form-control" value="{{ old('incident_date', $incident->incident_date ? $incident->incident_date->format('Y-m-d\TH:i') : '') }}" {{ !$canEditFully ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Update Photo</label>
                            <input type="file" name="image" class="form-control" accept="image/*" {{ !$canEditFully ? 'disabled' : '' }}>
                            @if($incident->image_path)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $incident->image_path) }}" style="max-height:100px;border-radius:8px;border:1px solid #e2e8f0;">
                            </div>
                            @endif
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" {{ !$canEditFully ? 'readonly' : '' }} required>{{ old('description', $incident->description) }}</textarea>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-top pt-4"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Location</h6>
                    @if($canEditFully)
                    <p class="text-muted small mb-2">Search or click to update</p>
                    @endif
                    <div class="mb-3">
                        <input type="text" name="location" id="locationText" class="form-control" value="{{ old('location', $incident->location) }}" {{ !$canEditFully ? 'readonly' : '' }} required>
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $incident->latitude) }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $incident->longitude) }}">
                    </div>
                    <div id="locationMap" class="map-container mb-4" style="height:320px; border-radius:8px; {{ !$canEditFully ? 'pointer-events:none;opacity:0.75;' : '' }}"></div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i> Cancel</a>
                        <button type="submit" class="btn btn-sg px-4"><i class="bi bi-check-lg me-1"></i> Update Incident</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var lat = parseFloat(document.getElementById('latitude').value) || 7.1907;
    var lng = parseFloat(document.getElementById('longitude').value) || 125.4553;
    var zoom = document.getElementById('latitude').value ? 15 : 12;
    var davaoBounds = L.latLngBounds([
        [6.8000, 125.1000],
        [7.5000, 125.7000]
    ]);
    var map = L.map('locationMap', {
        maxBounds: davaoBounds,
        maxBoundsViscosity: 1.0,
        minZoom: 10
    }).setView([lat, lng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker = document.getElementById('latitude').value ? L.marker([lat, lng]).addTo(map) : null;
    var canEditFully = {{ $canEditFully ? 'true' : 'false' }};

    if (canEditFully && typeof L.Control.Geocoder !== 'undefined') {
        L.Control.geocoder({ defaultMarkGeocode: false, placeholder: 'Search location...' })
            .on('markgeocode', function(e) {
                if (marker) map.removeLayer(marker);
                marker = L.marker(e.geocode.center).addTo(map);
                map.setView(e.geocode.center, 16);
                document.getElementById('latitude').value = e.geocode.center.lat.toFixed(7);
                document.getElementById('longitude').value = e.geocode.center.lng.toFixed(7);
                document.getElementById('locationText').value = e.geocode.name;
            }).addTo(map);
    }

    if (canEditFully) {
        map.on('click', function(e) {
            if (marker) map.removeLayer(marker);
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('latitude').value = e.latlng.lat.toFixed(7);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(7);
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + e.latlng.lat + '&lon=' + e.latlng.lng)
                .then(r => r.json()).then(data => {
                    document.getElementById('locationText').value = data.display_name || (e.latlng.lat.toFixed(5) + ', ' + e.latlng.lng.toFixed(5));
                }).catch(() => {
                    document.getElementById('locationText').value = e.latlng.lat.toFixed(5) + ', ' + e.latlng.lng.toFixed(5);
                });
        });
    }
});
</script>
@endsection
