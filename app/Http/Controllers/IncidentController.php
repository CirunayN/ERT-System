<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Team;
use App\Models\IncidentAssignment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->isCitizen()) {
            $incidents = Incident::where('user_id', $user->id)->latest()->get();
        } else {
            $incidents = Incident::latest()->get();
        }

        $criticalCount = $incidents->where('danger_level', 'Critical')->count();
        $pendingCount = $incidents->where('status', 'Pending')->count();
        $inProgressCount = $incidents->where('status', 'In Progress')->count();
        $resolvedCount = $incidents->where('status', 'Resolved')->count();

        return view('incidents.index', compact('incidents', 'criticalCount', 'pendingCount', 'inProgressCount', 'resolvedCount'));
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

        return redirect()->route('incidents.index')->with('success', 'Incident reported successfully!');
    }

    public function show(Incident $incident)
    {
        if (auth()->user()->isCitizen() && $incident->user_id !== auth()->id()) {
            abort(403);
        }
        $incident->load(['assignments.team', 'assignments.dispatcher', 'reporter']);
        $teams = Team::where('availability_status', 'Available')->get();
        return view('incidents.show', compact('incident', 'teams'));
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

        ActivityLog::log('incident_updated', "Incident #{$incident->id} updated at {$incident->location}", 'bi-pencil-square', 'info');

        return redirect()->route('incidents.index')->with('success', 'Incident updated successfully!');
    }

    public function destroy(Incident $incident)
    {
        $user = auth()->user();
        if ($user->isDispatcher()) {
            abort(403, 'Dispatchers cannot delete incident records.');
        }
        if ($user->isCitizen() && $incident->user_id !== $user->id) {
            abort(403);
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

        $incident->assignments()->where('status', 'active')->update(['status' => 'reassigned']);

        $team = Team::find($request->team_id);

        IncidentAssignment::create([
            'incident_id' => $incident->id,
            'team_id' => $request->team_id,
            'assigned_by' => Auth::id(),
            'assignment_date' => now(),
            'status' => 'active',
        ]);

        $incident->update(['status' => 'In Progress']);

        ActivityLog::log('team_dispatched', "{$team->team_name} dispatched to Incident #{$incident->id} at {$incident->location}", 'bi-send-fill', 'success');

        return redirect()->route('incidents.show', $incident)->with('success', 'Team assigned successfully!');
    }
}
