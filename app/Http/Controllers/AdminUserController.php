<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Display all users (Admin only)
     */
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }

    /**
     * Update a user's role
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:citizen,responder,dispatcher,admin',
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->route('admin.users')->with('success', 'User role updated successfully!');
    }

    /**
     * Delete a user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account!');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }
}
