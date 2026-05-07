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
        } elseif ($user->isResponder() && $user->team_id) {
            $query = Incident::where('emergency_type', $user->team->team_type);
        } else {
            $query = Incident::query();
        }

        $activeQuery = clone $query;
        $activeQuery->where('status', '!=', 'Completed');

        $totalIncidents = $activeQuery->count();
        $pendingCount = (clone $activeQuery)->where('status', 'Pending')->count();
        $criticalCount = (clone $activeQuery)->where('danger_level', 'Critical')->count();
        $resolvedCount = (clone $query)->where('status', 'Completed')->count();
        $inProgressCount = (clone $activeQuery)->where('status', 'In Progress')->count();
        $recentIncidents = (clone $activeQuery)->latest()->take(5)->get();

        //Cords sa mga incident na naa sa map
        $mapIncidents = (clone $activeQuery)->whereNotNull('latitude')->whereNotNull('longitude')->get();

        // Mag pakuha ug activity logs depende sa role
        $activityLogs = collect();
        $responderNotifications = collect();
        
        if ($user->isAdmin() || $user->isDispatcher()) {
            $activityLogs = ActivityLog::with('user')->latest()->take(15)->get();
        } elseif ($user->isResponder() && $user->team) {
            $responderNotifications = ActivityLog::where('description', 'like', "%{$user->team->team_name}%")
                ->orWhere('description', 'like', "%{$user->team->team_type}%")
                ->orWhere('description', 'like', "%New incident reported%")
                ->latest()->take(10)->get();
        }

        return view('dashboard', compact(
            'totalIncidents', 'pendingCount', 'criticalCount', 'resolvedCount',
            'inProgressCount', 'recentIncidents', 'mapIncidents', 'activityLogs', 'responderNotifications'
        ));
    }
}
