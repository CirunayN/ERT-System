<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isCitizen()) {
            $query = Incident::where('user_id', $user->id);
        } else {
            $query = Incident::query();
        }

        $totalIncidents = (clone $query)->count();
        $pendingCount = (clone $query)->where('status', 'Pending')->count();
        $criticalCount = (clone $query)->where('danger_level', 'Critical')->count();
        $resolvedCount = (clone $query)->where('status', 'Resolved')->count();
        $inProgressCount = (clone $query)->where('status', 'In Progress')->count();
        $recentIncidents = (clone $query)->latest()->take(5)->get();

        // Map markers — incidents with coordinates
        $mapIncidents = (clone $query)->whereNotNull('latitude')->whereNotNull('longitude')->get();

        // Activity logs
        $activityLogs = ActivityLog::with('user')->latest()->take(15)->get();

        return view('dashboard', compact(
            'totalIncidents', 'pendingCount', 'criticalCount', 'resolvedCount',
            'inProgressCount', 'recentIncidents', 'mapIncidents', 'activityLogs'
        ));
    }
}
