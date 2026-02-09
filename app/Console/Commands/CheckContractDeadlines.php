<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

class CheckContractDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check retainer contract deadlines and log notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        
        // H-30 Notifications
        $deadline30 = Client::where('category', 'Retainer')
            ->whereNotNull('retainer_contract_end')
            ->whereDate('retainer_contract_end', $today->copy()->addDays(30))
            ->get();

        foreach ($deadline30 as $client) {
            Log::warning("CONTRACT ALERT (H-30): {$client->name} contract expires on {$client->retainer_contract_end->format('Y-m-d')}");
            $this->info("⚠️  H-30: {$client->name}");
        }

        // H-7 Daily Notifications
        $deadline7 = Client::where('category', 'Retainer')
            ->whereNotNull('retainer_contract_end')
            ->whereBetween('retainer_contract_end', [$today, $today->copy()->addDays(7)])
            ->get();

        foreach ($deadline7 as $client) {
            $daysLeft = $today->diffInDays($client->retainer_contract_end);
            Log::warning("CONTRACT ALERT (H-{$daysLeft}): {$client->name} contract expires on {$client->retainer_contract_end->format('Y-m-d')}");
            $this->error("🚨 H-{$daysLeft}: {$client->name}");
        }

        $this->info("Contract deadline check completed.");
        
        return Command::SUCCESS;
    }
}
