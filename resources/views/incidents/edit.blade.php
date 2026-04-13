@extends('layouts.app')
@section('title', 'Edit Incident #' . $incident->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm">
            <div class="card-header py-3" style="background: linear-gradient(135deg, #d69e2e, #b7791f); border: none;">
                <h5 class="text-white fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i> Edit Incident Report</h5>
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger py-2">@foreach($errors->all() as $error)<div class="small">{{ $error }}</div>@endforeach</div>
                @endif
                <form action="{{ route('incidents.update', $incident) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reporter Name <span class="text-danger">*</span></label>
                            <input type="text" name="reporter_name" class="form-control" value="{{ old('reporter_name', $incident->reporter_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $incident->contact_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Incident Type <span class="text-danger">*</span></label>
                            <select name="emergency_type" class="form-select" required>
                                @foreach(['Fire', 'Medical', 'Accident', 'Natural Disaster', 'Crime', 'Rescue', 'Other'] as $type)
                                    <option value="{{ $type }}" {{ old('emergency_type', $incident->emergency_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Danger Level <span class="text-danger">*</span></label>
                            <select name="danger_level" class="form-select" required>
                                @foreach(['Low', 'Medium', 'High', 'Critical'] as $level)
                                    <option value="{{ $level }}" {{ old('danger_level', $incident->danger_level) == $level ? 'selected' : '' }}>{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                @foreach(['Pending', 'In Progress', 'Resolved'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $incident->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Incident Date/Time</label>
                            <input type="datetime-local" name="incident_date" class="form-control" value="{{ old('incident_date', $incident->incident_date ? $incident->incident_date->format('Y-m-d\TH:i') : '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Update Photo</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @if($incident->image_path)
                                <div class="mt-2"><img src="{{ asset('storage/' . $incident->image_path) }}" style="max-height:100px;border-radius:8px;border:2px solid #e2e8f0;"></div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Location — Click map to update</label>
                            <input type="text" name="location" id="locationText" class="form-control mb-2" value="{{ old('location', $incident->location) }}" required>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $incident->latitude) }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $incident->longitude) }}">
                            <div id="locationMap" class="map-container" style="height: 300px;"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $incident->description) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Cancel</a>
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
    var lat = parseFloat(document.getElementById('latitude').value) || 14.5995;
    var lng = parseFloat(document.getElementById('longitude').value) || 120.9842;
    var zoom = document.getElementById('latitude').value ? 15 : 6;
    var map = L.map('locationMap').setView([lat, lng], zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    var marker = document.getElementById('latitude').value ? L.marker([lat, lng]).addTo(map) : null;

    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('latitude').value = e.latlng.lat.toFixed(7);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(7);
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + e.latlng.lat + '&lon=' + e.latlng.lng)
            .then(r => r.json()).then(data => {
                if (data.display_name) document.getElementById('locationText').value = data.display_name;
            }).catch(() => { document.getElementById('locationText').value = e.latlng.lat.toFixed(5) + ', ' + e.latlng.lng.toFixed(5); });
    });
});
</script>
@endsection
