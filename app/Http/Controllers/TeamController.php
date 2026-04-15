<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount('members', 'assignments')->get();
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_name' => 'required|string|max:255',
            'team_type' => 'required|string',
            'availability_status' => 'required|string',
        ]);

        $team = Team::create($validated);
        ActivityLog::log('team_created', "Team \"{$team->team_name}\" ({$team->team_type}) created", 'bi-people-fill', 'primary');

        return redirect()->route('teams.index')->with('success', 'Team created successfully!');
    }

    public function edit(Team $team)
    {
        $team->load('members');
        $availableResponders = User::where('role', 'responder')
                                   ->whereNull('team_id')
                                   ->get();
        return view('teams.edit', compact('team', 'availableResponders'));
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'team_name' => 'required|string|max:255',
            'team_type' => 'required|string',
            'availability_status' => 'required|string',
        ]);

        $team->update($validated);
        ActivityLog::log('team_updated', "Team \"{$team->team_name}\" updated", 'bi-pencil-square', 'info');

        return redirect()->route('teams.index')->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team)
    {
        ActivityLog::log('team_deleted', "Team \"{$team->team_name}\" deleted", 'bi-trash', 'danger');
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted successfully!');
    }

    public function addResponder(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Ensure user is a responder and not already in a team
        if ($user->role !== 'responder') {
            return back()->with('error', 'Only responders can be assigned to a team.');
        }

        $user->update(['team_id' => $team->id]);

        ActivityLog::log('responder_added', "Responder \"{$user->name}\" added to {$team->team_name}", 'bi-person-plus-fill', 'success');

        return redirect()->route('teams.edit', $team)->with('success', 'Responder added!');
    }

    public function removeResponder(Team $team, User $member)
    {
        ActivityLog::log('responder_removed', "Responder \"{$member->name}\" removed from {$team->team_name}", 'bi-person-dash', 'warning');
        $member->update(['team_id' => null]);
        return redirect()->route('teams.edit', $team)->with('success', 'Responder removed!');
    }
}
