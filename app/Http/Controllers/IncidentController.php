<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Team;
use App\Models\IncidentAssignment;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\SystemAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Incident::query();

        if ($user->isCitizen()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isResponder() && $user->team_id) {
            $query->where('emergency_type', $user->team->team_type);
        }

        if (request()->has('danger_level') && request('danger_level') !== 'All' && request('danger_level') !== '') {
            $query->where('danger_level', request('danger_level'));
        }

        if (request()->has('status') && request('status') !== 'All' && request('status') !== '') {
            $query->where('status', request('status'));
        }

        $incidents = $query->latest()->get();

        $criticalCount = $incidents->where('danger_level', 'Critical')->count();
        $pendingCount = $incidents->where('status', 'Pending')->count();
        $inProgressCount = $incidents->where('status', 'In Progress')->count();
        $resolvedCount = Incident::where('status', 'Completed')->count();

        // active incidents only for index module
        $incidents = $incidents->where('status', '!=', 'Completed')->values();

        return view('incidents.index', compact('incidents', 'criticalCount', 'pendingCount', 'inProgressCount', 'resolvedCount'));
    }

    public function history()
    {
        $user = auth()->user();
        $query = Incident::where('status', 'Completed');

        if ($user->isCitizen()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isResponder() && $user->team_id) {
            $query->where('emergency_type', $user->team->team_type);
        }

        if (request()->has('date_from') && request('date_from')) {
            $query->whereDate('updated_at', '>=', request('date_from'));
        }
        if (request()->has('date_to') && request('date_to')) {
            $query->whereDate('updated_at', '<=', request('date_to'));
        }

        $incidents = $query->latest()->get();

        return view('incidents.history', compact('incidents'));
    }

    public function create()
    {
        return view('incidents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reporter_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'emergency_type' => 'required|string',
            'danger_level' => 'required|string',
            'incident_date' => 'nullable|date',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'Pending';

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('incidents', 'public');
        }

        unset($validated['image']);
        $incident = Incident::create($validated);

        ActivityLog::log('incident_created', "New incident reported: \"{$incident->title_display}\" at {$incident->location}", 'bi-exclamation-triangle-fill', 'warning');

        // Notify Admins, Responders, Dispatchers
        $usersToNotify = User::whereIn('role', ['admin', 'responder', 'dispatcher'])->get();
        foreach ($usersToNotify as $userToNotify) {
            $userToNotify->notify(new SystemAlert('New Incident Reported', "{$incident->emergency_type} at {$incident->location}.", route('incidents.show', $incident), 'bi-exclamation-octagon'));
        }

        return redirect()->route('incidents.index')->with('success', 'Incident reported successfully!');
    }

    public function show(Incident $incident)
    {
        if (auth()->user()->isCitizen() && $incident->user_id !== auth()->id()) {
            abort(403);
        }
        $incident->load(['assignments.team', 'assignments.dispatcher', 'reporter']);
        return view('incidents.show', compact('incident'));
    }

    public function manage(Incident $incident)
    {
        if (auth()->user()->isCitizen()) {
            abort(403);
        }
        
        $incident->load(['assignments.team', 'assignments.dispatcher', 'reporter', 'currentAssignment.team']);
        // Fetch teams with their active assignments so we can show what they are doing
        $teams = Team::with(['assignments' => function($q) {
            $q->where('status', 'active')->with('incident');
        }])->get();
        
        return view('incidents.manage', compact('incident', 'teams'));
    }

    public function edit(Incident $incident)
    {
        if (auth()->user()->isCitizen() && $incident->user_id !== auth()->id()) {
            abort(403);
        }
        return view('incidents.edit', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        if (auth()->user()->isCitizen() && $incident->user_id !== auth()->id()) {
            abort(403);
        }

        $canEditFully = auth()->user()->isAdmin() || $incident->user_id === auth()->id();

        if ($canEditFully) {
            $validated = $request->validate([
                'reporter_name' => 'required|string|max:255',
                'contact_number' => 'nullable|string|max:20',
                'location' => 'required|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'emergency_type' => 'required|string',
                'danger_level' => 'required|string',
                'status' => 'required|string',
                'incident_date' => 'nullable|date',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            if ($request->hasFile('image')) {
                if ($incident->image_path) {
                    Storage::disk('public')->delete($incident->image_path);
                }
                $validated['image_path'] = $request->file('image')->store('incidents', 'public');
            }

            unset($validated['image']);
            $incident->update($validated);
        } else {
            $validated = $request->validate([
                'status' => 'required|string',
            ]);
            $incident->update(['status' => $validated['status']]);
        }

        ActivityLog::log('incident_updated', "Incident #{$incident->id} updated at {$incident->location}", 'bi-pencil-square', 'info');

        return redirect()->route('incidents.index')->with('success', 'Incident updated successfully!');
    }

    public function destroy(Incident $incident)
    {
        $user = auth()->user();
        if ($user->isDispatcher() || $user->isResponder()) {
            abort(403, 'Privilege level lacking to delete incident records.');
        }
        if ($user->isCitizen()) {
            abort(403, 'Citizens cannot delete incident records. They must be saved in the system.');
        }

        if ($incident->image_path) {
            Storage::disk('public')->delete($incident->image_path);
        }

        ActivityLog::log('incident_deleted', "Incident #{$incident->id} \"{$incident->reporter_name}\" deleted", 'bi-trash', 'danger');

        $incident->delete();
        return redirect()->route('incidents.index')->with('success', 'Incident deleted successfully!');
    }

    public function assignTeam(Request $request, Incident $incident)
    {
        $request->validate(['team_id' => 'required|exists:teams,id']);

        // Mark previous assignments as reassigned
        $previousAssignments = $incident->assignments()->where('status', 'active')->get();
        foreach ($previousAssignments as $prev) {
            $prev->update(['status' => 'reassigned']);
            // If the old team has no other active assignments, we might want to make them available, but let's just make sure the new team becomes unavailable.
            if ($prev->team) {
                 $prev->team->update(['availability_status' => 'Available']);
            }
        }

        $team = Team::find($request->team_id);
        
        if ($team->availability_status === 'Unavailable' || $team->assignments()->where('status', 'active')->exists()) {
             return redirect()->back()->with('error', 'Team is currently deployed to another incident and unavailable.');
        }

        IncidentAssignment::create([
            'incident_id' => $incident->id,
            'team_id' => $request->team_id,
            'assigned_by' => Auth::id(),
            'assignment_date' => now(),
            'status' => 'active',
        ]);

        $incident->update(['status' => 'In Progress']);
        $team->update(['availability_status' => 'Unavailable']);

        ActivityLog::log('team_dispatched', "{$team->team_name} dispatched to Incident #{$incident->id} at {$incident->location}", 'bi-send-fill', 'success');

        // Notify Responders in that team
        $responders = User::where('team_id', $team->id)->get();
        foreach ($responders as $responder) {
            $responder->notify(new SystemAlert("Team Dispatched!", "You have been assigned to Incident #{$incident->id}.", route('incidents.show', $incident), 'bi-truck'));
        }

        return redirect()->route('incidents.manage', $incident)->with('success', 'Team assigned successfully!');
    }

    public function updateStatus(Request $request, Incident $incident)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,En Route,On Scene,Completed'
        ]);

        $incident->update(['status' => $request->status]);

        // Specific styling for ActivityLog based on new Responder flow
        $msg = "Incident #{$incident->id} {$incident->emergency_type} status updated to {$request->status}";
        $icon = 'bi-info-circle';
        
        switch($request->status) {
            case 'En Route': $icon = 'bi-truck'; break;
            case 'On Scene': $icon = 'bi-geo-alt-fill'; break;
            case 'Completed': 
                $icon = 'bi-check-circle-fill'; 
                
                // When an incident is completed, mark the assignment as completed and the team as available
                $activeAssignments = $incident->assignments()->where('status', 'active')->get();
                foreach($activeAssignments as $assignment) {
                    $assignment->update(['status' => 'completed']);
                    if ($assignment->team) {
                        $assignment->team->update(['availability_status' => 'Available']);
                    }
                }
                break;
            case 'In Progress': $icon = 'bi-arrow-repeat'; break;
        }

        if (auth()->user()->isResponder() && $request->status == 'In Progress') {
             $msg = "Responder \"".auth()->user()->name."\" accepted Incident #{$incident->id}";
             $icon = 'bi-person-check-fill';
        }

        ActivityLog::log('status_update', $msg, $icon, 'primary');

        if ($request->status === 'Completed' && $incident->reporter) {
            $incident->reporter->notify(new SystemAlert("Incident Resolved", "Your report for {$incident->emergency_type} has been resolved.", route('incidents.show', $incident), 'bi-check-circle-fill'));
        }

        return redirect()->back()->with('success', 'Status updated successfully!');
    }

    public function verify(Incident $incident)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isDispatcher()) {
            abort(403);
        }

        $incident->update(['is_verified' => true]);
        
        if ($incident->reporter) {
            $incident->reporter->notify(new SystemAlert("Report Verified", "Your incident report #{$incident->id} has been verified by the dispatch center.", route('incidents.show', $incident), 'bi-shield-check'));
        }

        ActivityLog::log('incident_verified', "Incident #{$incident->id} has been fully verified.", 'bi-shield-check', 'success');

        return redirect()->back()->with('success', 'Incident verified successfully!');
    }
}
