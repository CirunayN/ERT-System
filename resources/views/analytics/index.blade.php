@extends('layouts.app')
@section('title', 'Reports & Analytics')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Reports & Analytics</h4>
    <p class="text-muted mb-0">Statistical reports about incidents and system performance</p>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md col-6">
        <div class="stat-card total"><div class="stat-label">Total Incidents</div><div class="stat-number">{{ $totalIncidents }}</div><div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div></div>
    </div>
    <div class="col-md col-6">
        <div class="stat-card in-progress"><div class="stat-label">Teams</div><div class="stat-number">{{ $totalTeams }}</div><div class="stat-icon"><i class="bi bi-people"></i></div></div>
    </div>
    <div class="col-md col-6">
        <div class="stat-card pending"><div class="stat-label">Assignments</div><div class="stat-number">{{ $totalAssignments }}</div><div class="stat-icon"><i class="bi bi-send"></i></div></div>
    </div>
    <div class="col-md col-6">
        <div class="stat-card resolved"><div class="stat-label">Resolution Rate</div><div class="stat-number">{{ $resolutionRate }}%</div><div class="stat-icon"><i class="bi bi-check-circle"></i></div></div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i>Incidents Over Last 7 Days</h6></div>
            <div class="card-body"><canvas id="lineChart" height="120"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2"></i>By Type</h6></div>
            <div class="card-body d-flex align-items-center justify-content-center"><canvas id="pieChart"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2"></i>By Status</h6></div>
            <div class="card-body"><canvas id="barChart" height="160"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="bi bi-bullseye me-2"></i>By Danger Level</h6></div>
            <div class="card-body d-flex align-items-center justify-content-center"><canvas id="doughnutChart"></canvas></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Line Chart - Incidents over time
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Incidents',
                data: @json($dailyCounts),
                borderColor: '#e67e22',
                backgroundColor: 'rgba(230,126,34,0.1)',
                fill: true, tension: 0.4, pointRadius: 5, pointBackgroundColor: '#e67e22',
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Pie Chart - By Type
    var byType = @json($byType);
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(byType),
            datasets: [{ data: Object.values(byType), backgroundColor: ['#e53e3e','#3182ce','#d69e2e','#38a169','#805ad5','#dd6b20','#718096'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
    });

    // Bar Chart - By Status
    var byStatus = @json($byStatus);
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(byStatus),
            datasets: [{ label: 'Count', data: Object.values(byStatus), backgroundColor: ['#d69e2e','#3182ce','#38a169'] }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Doughnut Chart - By Danger Level
    var byDanger = @json($byDanger);
    new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(byDanger),
            datasets: [{ data: Object.values(byDanger), backgroundColor: ['#38a169','#d69e2e','#dd6b20','#e53e3e'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
    });
});
</script>
@endsection
