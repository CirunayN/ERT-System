<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Team;
use App\Models\IncidentAssignment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = IncidentAssignment::with(['incident', 'team', 'dispatcher'])->latest()->get();
        $incidents = Incident::where('status', '!=', 'Resolved')->get();
        $teams = Team::where('availability_status', 'Available')->get();
        return view('assignments.index', compact('assignments', 'incidents', 'teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'incident_id' => 'required|exists:incidents,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $incident = Incident::findOrFail($request->incident_id);
        $team = Team::findOrFail($request->team_id);

        // Gi update and old assigned teams to 'reassigned'
        $incident->assignments()->where('status', 'active')->update(['status' => 'reassigned']);

        IncidentAssignment::create([
            'incident_id' => $incident->id,
            'team_id' => $team->id,
            'assigned_by' => auth()->id(),
            'assignment_date' => now(),
            'status' => 'active',
        ]);

        $incident->update(['status' => 'In Progress']);

        ActivityLog::log('team_dispatched', "{$team->team_name} dispatched to Incident #{$incident->id}", 'bi-send-fill', 'success');

        return redirect()->route('assignments.index')->with('success', "{$team->team_name} assigned to Incident #{$incident->id}!");
    }
}
