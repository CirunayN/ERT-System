<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Team;
use App\Models\IncidentAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Incidents by type
        $byType = Incident::selectRaw('emergency_type, COUNT(*) as count')
            ->groupBy('emergency_type')->pluck('count', 'emergency_type');

        // Incidents by status
        $byStatus = Incident::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        // Incidents by danger level
        $byDanger = Incident::selectRaw('danger_level, COUNT(*) as count')
            ->groupBy('danger_level')->pluck('count', 'danger_level');

        // Incidents over the last 7 days
        $dailyCounts = [];
        $dailyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('M d');
            $dailyCounts[] = Incident::whereDate('created_at', $date->toDateString())->count();
        }

        // Summary stats
        $totalIncidents = Incident::count();
        $totalTeams = Team::count();
        $totalAssignments = IncidentAssignment::count();
        $totalUsers = User::count();
        $resolvedCount = Incident::where('status', 'Resolved')->count();
        $resolutionRate = $totalIncidents > 0 ? round(($resolvedCount / $totalIncidents) * 100, 1) : 0;

        return view('analytics.index', compact(
            'byType', 'byStatus', 'byDanger', 'dailyCounts', 'dailyLabels',
            'totalIncidents', 'totalTeams', 'totalAssignments', 'totalUsers', 'resolutionRate'
        ));
    }
}
