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
        $clients = Client::latest()->get();
        return view('clients.index', compact('clients'));
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
        ]);

        Client::create($validated);

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
        ]);

        $client->update($validated);

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
