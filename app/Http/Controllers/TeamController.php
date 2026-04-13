<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Responder;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount('responders', 'assignments')->get();
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
        $team->load('responders');
        return view('teams.edit', compact('team'));
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
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $team->responders()->create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        ActivityLog::log('responder_added', "Responder \"{$request->name}\" added to {$team->team_name}", 'bi-person-plus-fill', 'success');

        return redirect()->route('teams.edit', $team)->with('success', 'Responder added!');
    }

    public function removeResponder(Team $team, Responder $responder)
    {
        ActivityLog::log('responder_removed', "Responder \"{$responder->name}\" removed from {$team->team_name}", 'bi-person-dash', 'warning');
        $responder->delete();
        return redirect()->route('teams.edit', $team)->with('success', 'Responder removed!');
    }
}
