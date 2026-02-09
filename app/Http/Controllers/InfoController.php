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
            'image' => 'nullable|image|max:5120', // Max 5MB
            'expires_at' => 'nullable|date|after:today',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_public'] = $request->has('is_public');

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('infos', 'public');
            $validated['image'] = $path;
        }

        Info::create($validated);

        return redirect()->route('infos.index')->with('success', 'Info created successfully.');
    }

    public function update(Request $request, Info $info)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_public' => 'boolean',
            'image' => 'nullable|image|max:5120',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $validated['is_public'] = $request->has('is_public');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($info->image && \Storage::disk('public')->exists($info->image)) {
                \Storage::disk('public')->delete($info->image);
            }
            $path = $request->file('image')->store('infos', 'public');
            $validated['image'] = $path;
        }

        $info->update($validated);

        return redirect()->route('infos.index')->with('success', 'Info updated successfully.');
    }

    public function destroy(Info $info)
    {
        $info->delete();
        return redirect()->route('infos.index')->with('success', 'Info deleted successfully.');
    }
}
