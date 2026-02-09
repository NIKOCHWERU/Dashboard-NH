<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private $client;
    private $service;
    
    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/google-drive-service-account.json'));
        $this->client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
        $this->service = new Google_Service_Calendar($this->client);
    }
    
    /**
     * Get Indonesian holidays from Google Calendar
     * 
     * @param int|null $year
     * @return \Illuminate\Support\Collection
     */
    public function getIndonesianHolidays($year = null)
    {
        $year = $year ?? date('Y');
        $cacheKey = "holidays_{$year}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            $calendarId = env('GOOGLE_CALENDAR_HOLIDAYS_ID', 'en.indonesian#holiday@group.v.calendar.google.com');
            
            $optParams = [
                'timeMin' => "{$year}-01-01T00:00:00Z",
                'timeMax' => ($year + 1) . "-01-01T00:00:00Z",
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ];
            
            $results = $this->service->events->listEvents($calendarId, $optParams);
            
            $holidays = collect($results->getItems())->map(function($event) {
                return [
                    'date' => $event->start->date,
                    'title' => $event->summary,
                ];
            });
            
            if ($holidays->isNotEmpty()) {
                Cache::put($cacheKey, $holidays, now()->addMonth());
                return $holidays;
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch holidays from Google Calendar: ' . $e->getMessage());
        }

        // Fallback to hardcoded holidays if API fails
        $fallback = $this->getFallbackHolidays($year);
        if ($fallback->isNotEmpty()) {
            Cache::put($cacheKey, $fallback, now()->addDay()); // Cache fallback for a shorter time
            return $fallback;
        }

        return collect([]);
    }

    private function getFallbackHolidays($year)
    {
        if ($year == 2026) {
            return collect([
                ['date' => '2026-01-01', 'title' => 'Tahun Baru 2026 Masehi'],
                ['date' => '2026-01-16', 'title' => 'Isra Mikraj Nabi Muhammad S.A.W.'],
                ['date' => '2026-02-17', 'title' => 'Tahun Baru Imlek 2576 Kongzili'],
                ['date' => '2026-03-19', 'title' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)'],
                ['date' => '2026-03-21', 'title' => 'Idulfitri 1447 Hijriah'],
                ['date' => '2026-03-22', 'title' => 'Idulfitri 1447 Hijriah'],
                ['date' => '2026-04-03', 'title' => 'Wafat Yesus Kristus'],
                ['date' => '2026-04-05', 'title' => 'Kebangkitan Yesus Kristus (Paskah)'],
                ['date' => '2026-05-01', 'title' => 'Hari Buruh Internasional'],
                ['date' => '2026-05-14', 'title' => 'Kenaikan Yesus Kristus'],
                ['date' => '2026-05-27', 'title' => 'Iduladha 1447 Hijriah'],
                ['date' => '2026-05-31', 'title' => 'Hari Raya Waisak 2570 BE'],
                ['date' => '2026-06-01', 'title' => 'Hari Lahir Pancasila'],
                ['date' => '2026-06-16', 'title' => 'Tahun Baru Islam 1448 Hijriah'],
                ['date' => '2026-08-17', 'title' => 'Proklamasi Kemerdekaan'],
                ['date' => '2026-08-25', 'title' => 'Maulid Nabi Muhammad S.A.W'],
                ['date' => '2026-12-25', 'title' => 'Kelahiran Yesus Kristus'],
                // Cuti Bersama
                ['date' => '2026-02-16', 'title' => 'Cuti Bersama (Tahun Baru Imlek)'],
                ['date' => '2026-03-18', 'title' => 'Cuti Bersama (Hari Suci Nyepi)'],
                ['date' => '2026-03-20', 'title' => 'Cuti Bersama (Idulfitri)'],
                ['date' => '2026-03-23', 'title' => 'Cuti Bersama (Idulfitri)'],
                ['date' => '2026-03-24', 'title' => 'Cuti Bersama (Idulfitri)'],
                ['date' => '2026-05-15', 'title' => 'Cuti Bersama (Kenaikan Yesus Kristus)'],
                ['date' => '2026-05-28', 'title' => 'Cuti Bersama (Iduladha)'],
                ['date' => '2026-12-26', 'title' => 'Cuti Bersama (Kelahiran Yesus Kristus)'],
            ]);
        }
        return collect([]);
    }
}
