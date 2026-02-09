<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('clients')->latest()->get();
        $clients = Client::all();
        return view('users.index', compact('users', 'clients'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'User role updated.');
    }

    public function assignClient(Request $request, User $user)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $user->clients()->syncWithoutDetaching($request->client_id);

        return redirect()->back()->with('success', 'Client assigned to user.');
    }

    public function destroy(User $user)
    {
        // Option to soft delete or just remove clients
        $user->delete();
        return redirect()->back()->with('success', 'User deleted.');
    }

    public function toggleEventPermission(User $user)
    {
        $user->can_manage_events = !$user->can_manage_events;
        $user->save();
        
        return redirect()->back()->with('success', 'Event permission updated successfully.');
    }
}
