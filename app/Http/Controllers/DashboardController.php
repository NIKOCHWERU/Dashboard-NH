<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\File;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $driveService;

    public function index(GoogleDriveService $driveService, \App\Services\GoogleCalendarService $calendarService)
    {
        // 1. Get Usage from Google Drive Folder directly
        $totalUsage = \Illuminate\Support\Facades\Cache::remember('drive_folder_usage', 600, function () use ($driveService) {
            try {
                return $driveService->getTotalFolderSize();
            } catch (\Exception $e) {
                return File::sum('size');
            }
        });

        $limitGb = env('GOOGLE_STORAGE_LIMIT_GB', 15);
        $totalLimit = $limitGb * 1024 * 1024 * 1024;

        $stats = [
            'total_clients' => Client::count(),
            'total_files' => File::count(),
            'total_users' => User::count(),
            'storage' => [
                'usage' => $totalUsage,
                'limit' => $totalLimit
            ],
        ];
        
        // 2. Calendar Events for Mini Calendar
        $events = \App\Models\Event::with('user')->get();
        $holidays = $calendarService->getIndonesianHolidays(date('Y'));
        
        $calendarEvents = $events->map(function($event) {
            return [
                'title' => $event->title,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end ? $event->end->toIso8601String() : null,
                'backgroundColor' => $this->getColorByType($event->type),
                'extendedProps' => ['type' => $event->type]
            ];
        });

        foreach ($holidays as $holiday) {
            $calendarEvents->push([
                'title' => $holiday['title'],
                'start' => $holiday['date'],
                'backgroundColor' => '#fee2e2',
                'display' => 'background',
                'extendedProps' => ['isHoliday' => true]
            ]);
        }

        // 3. Upcoming Events & Info
        $upcomingEvents = \App\Models\Event::with('user')
            ->whereBetween('start', [now(), now()->addDays(7)])
            ->orderBy('start')
            ->get();

        $contractDeadlines = Client::where('category', 'Retainer')
            ->whereNotNull('retainer_contract_end')
            ->whereBetween('retainer_contract_end', [now(), now()->addDays(30)])
            ->orderBy('retainer_contract_end')
            ->get();

        $infos = \App\Models\Info::with('creator')->latest()->take(3)->get();

        return view('dashboard', compact('stats', 'contractDeadlines', 'upcomingEvents', 'infos', 'calendarEvents'));
    }

    private function getColorByType($type)
    {
        return match($type) {
            'meeting' => '#22c55e',
            'deadline' => '#eab308',
            default => '#3b82f6',
        };
    }
}
