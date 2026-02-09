<?php

namespace App\Http\Controllers;

use App\Models\Info;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index()
    {
        $infos = Info::with('creator')->latest()->get();
        return view('infos.index', compact('infos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_public' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_public'] = $request->has('is_public');

        Info::create($validated);

        return redirect()->route('infos.index')->with('success', 'Info created successfully.');
    }

    public function update(Request $request, Info $info)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_public' => 'boolean',
        ]);

        $validated['is_public'] = $request->has('is_public');

        $info->update($validated);

        return redirect()->route('infos.index')->with('success', 'Info updated successfully.');
    }

    public function destroy(Info $info)
    {
        $info->delete();
        return redirect()->route('infos.index')->with('success', 'Info deleted successfully.');
    }
}
