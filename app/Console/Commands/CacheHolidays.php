<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CacheHolidays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holidays:cache {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and cache Indonesian holidays from Google Calendar';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\GoogleCalendarService $calendarService)
    {
        $year = $this->argument('year') ?? date('Y');
        $this->info("Fetching holidays for {$year}...");
        
        $holidays = $calendarService->getIndonesianHolidays($year);
        
        if ($holidays->isNotEmpty()) {
            \Illuminate\Support\Facades\Cache::put("holidays_{$year}", $holidays, now()->addYear());
            $this->info("Successfully cached " . $holidays->count() . " holidays for {$year}.");
        } else {
            $this->error("Failed to fetch holidays for {$year}. Check logs for details.");
            return 1;
        }

        return 0;
    }
}
