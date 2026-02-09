<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Services\GoogleDriveService;

class EventController extends Controller
{
    protected $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    public function index(\App\Services\GoogleCalendarService $calendarService)
    {
        $events = Event::with('user')->latest()->get();
        
        // Fetch holidays from Google Calendar (cached)
        $holidays = $calendarService->getIndonesianHolidays(2026);
        
        // Format for FullCalendar
        $calendarEvents = $events->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end ? $event->end->toIso8601String() : null,
                'backgroundColor' => $this->getColorByType($event->type),
                'extendedProps' => [
                    'type' => $event->type,
                    'photo' => $event->photo,
                ]
            ];
        });

        // Add holidays to calendar
        foreach ($holidays as $holiday) {
            $calendarEvents->push([
                'title' => $holiday['title'],
                'start' => $holiday['date'],
                'backgroundColor' => '#fee2e2', // Light red background
                'display' => 'background',
                'extendedProps' => [
                    'type' => 'holiday',
                    'isHoliday' => true,
                ]
            ]);
        }

        $canManage = auth()->user()->canManageEvents();

        return view('events.index', compact('events', 'calendarEvents', 'canManage'));
    }

    public function storeFromDate(Request $request)
    {
        if (!auth()->user()->canManageEvents()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        // Return success with date for modal pre-fill
        return response()->json([
            'success' => true,
            'date' => $validated['date']
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canManageEvents()) {
            return back()->with('error', 'You do not have permission to create events.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'type' => 'required|string',
            'photo' => 'nullable|image|max:10240', // Max 10MB
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('photo')) {
            try {
                $targetFolderId = $this->driveService->ensureFolderExists('OfficeApp/Events');
                $fileId = $this->driveService->uploadFile($request->file('photo'), $targetFolderId);
                $validated['photo'] = $this->driveService->getThumbnailUrl($fileId) ?? $this->driveService->getFileWebLink($fileId);
            } catch (\Exception $e) {
                return back()->with('error', 'Photo upload failed: ' . $e->getMessage());
            }
        }

        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function update(Request $request, Event $event)
    {
        if (!auth()->user()->canManageEvents()) {
            return back()->with('error', 'You do not have permission to update events.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'type' => 'required|string',
            'photo' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('photo')) {
            try {
                $targetFolderId = $this->driveService->ensureFolderExists('OfficeApp/Events');
                $fileId = $this->driveService->uploadFile($request->file('photo'), $targetFolderId);
                $validated['photo'] = $this->driveService->getThumbnailUrl($fileId) ?? $this->driveService->getFileWebLink($fileId);
            } catch (\Exception $e) {
                return back()->with('error', 'Photo upload failed: ' . $e->getMessage());
            }
        }

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        if (!auth()->user()->canManageEvents()) {
            return back()->with('error', 'You do not have permission to delete events.');
        }

        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    private function getColorByType($type)
    {
        return match($type) {
            'meeting' => '#3B82F6',
            'deadline' => '#EF4444',
            'general' => '#10B981',
            default => '#6B7280'
        };
    }
}
