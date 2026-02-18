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
        // 1. Get Usage from Database (sum of synced files)
        // This is faster and matches the "Total Files" count
        $totalUsage = \Illuminate\Support\Facades\Cache::remember('drive_usage_db', 60, function () {
            return File::sum('size');
        });

        $limitGb = env('GOOGLE_STORAGE_LIMIT_GB', 15);
        $totalLimit = $limitGb * 1024 * 1024 * 1024;

        $stats = [
            'total_clients' => Client::count(),
            'total_files' => File::count(),
            'total_users' => User::count(),
            'storage' => [
                'usage' => $totalUsage,
                'limit' => $totalLimit,
                'usage_gb' => round($totalUsage / (1024 * 1024 * 1024), 2),
                'limit_gb' => $limitGb,
                'percentage' => $totalLimit > 0 ? round(($totalUsage / $totalLimit) * 100, 1) : 0,
                'used_formatted' => $this->formatBytes($totalUsage),
                'limit_formatted' => $this->formatBytes($totalLimit),
            ],
        ];

        // 2. Calendar Events for Mini Calendar
        $events = \App\Models\Event::with(['user', 'category'])->get();
        $holidays = $calendarService->getIndonesianHolidays(date('Y'));

        $calendarEvents = $events->map(function ($event) {
            $color = $event->category ? $event->category->color : $this->getColorByType($event->type);
            return [
                'title' => $event->title,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end ? $event->end->toIso8601String() : null,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'type' => $event->type,
                    'categoryColor' => $color
                ]
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
        // a. Cuti (Leaves) - Filter by title containing 'Cuti' or type 'leave'
        $leaves = \App\Models\Event::with(['user', 'category'])
            ->where(function ($q) {
                $q->where('title', 'like', '%Cuti%')
                    ->orWhere('type', 'leave');
            })
            ->whereBetween('start', [now()->startOfMonth(), now()->endOfMonth()])
            ->orderBy('start')
            ->get();

        // b. Upcoming Meetings - Filter by type 'meeting'
        $upcomingMeetings = \App\Models\Event::with(['user', 'category'])
            ->where('type', 'meeting')
            ->where('type', 'meeting')
            ->whereBetween('start', [now(), now()->endOfMonth()])
            ->orderBy('start')
            ->get();

        $contractDeadlines = Client::where('category', 'Retainer')
            ->whereNotNull('retainer_contract_end')
            ->whereBetween('retainer_contract_end', [now(), now()->addMonths(1)])
            ->orderBy('retainer_contract_end')
            ->get();

        $infos = \App\Models\Info::with('creator')->active()->latest()->take(3)->get();

        return view('dashboard', compact('stats', 'contractDeadlines', 'upcomingMeetings', 'leaves', 'infos', 'calendarEvents'));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Calculate bytes 
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function getColorByType($type)
    {
        return match ($type) {
            'meeting' => '#22c55e',
            'deadline' => '#eab308',
            default => '#3b82f6',
        };
    }
}
