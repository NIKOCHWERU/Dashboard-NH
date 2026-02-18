<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::with('users')->latest()->get();
        $users = \App\Models\User::all();
        return view('clients.index', compact('clients', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:retainer,perorangan',
            'service_type' => 'nullable|string|max:255',
            'case_status' => 'nullable|string|max:255',
            'pic_id' => 'nullable|exists:users,id',
            'retainer_contract_end' => 'nullable|date',
            'status' => 'required|in:active,inactive,pending',
            'access_user_ids' => 'nullable|array',
            'access_user_ids.*' => 'exists:users,id',
        ]);

        $client = Client::create($validated);

        if ($request->has('access_user_ids')) {
            $client->users()->attach($request->access_user_ids);
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:retainer,perorangan',
            'service_type' => 'nullable|string|max:255',
            'case_status' => 'nullable|string|max:255',
            'pic_id' => 'nullable|exists:users,id',
            'retainer_contract_end' => 'nullable|date',
            'status' => 'required|in:active,inactive,pending',
            'access_user_ids' => 'nullable|array',
            'access_user_ids.*' => 'exists:users,id',
        ]);

        $client->update($validated);

        if ($request->has('access_user_ids')) {
            $client->users()->sync($request->access_user_ids);
        } else {
            // If field exists but empty array/null passed (e.g. unchecked all), sync empty
            // However, HTML forms don't send array if no checkboxes checked.
            // We should handle the case where it's missing from request but intended to be empty?
            // Usually simpler to just sync([]) if missing IF we know the form always submits it.
            // But let's assume if it's not present, we might not want to clear it?
            // Actually for checkbox list, standard pattern is to add a hidden input or handle it.
            // Let's assume the View sends an array or nothing.
            // Better to explicitly sync what is provided, or empty if we detect it's an update.
            // To be safe, let's use `sync` with the provided array or defaulting to []. 
            // But wait, if we use `if ($request->has('access_user_ids'))`, we skip if unchecked.
            // Strategy: Add `<input type="hidden" name="access_user_ids" value="" />` before checkboxes
            // OR just correct logic here:
            $client->users()->sync($request->input('access_user_ids', []));
        }

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
