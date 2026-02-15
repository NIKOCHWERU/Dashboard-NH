<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\File;
use App\Services\GoogleDriveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncDriveFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-drive {--client= : ID of specific client to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize files from Google Drive to Database';

    protected $driveService;

    /**
     * Execute the console command.
     */
    public function handle(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
        $clientId = $this->option('client');

        $this->info('Starting Google Drive Sync...');

        if ($clientId) {
            $clients = Client::where('id', $clientId)->get();
        } else {
            $clients = Client::all();
        }

        foreach ($clients as $client) {
            $this->syncClientFiles($client);
        }

        // Special handling for "Kantor Narasumber Hukum" (Public Category)
        // If no specific client is requested, or if the dummy client ID matches
        if (!$clientId) {
            $this->syncPublicCategory('Kantor Narasumber Hukum');
        }

        $this->info('Sync completed.');
    }

    private function syncClientFiles(Client $client)
    {
        $this->info("Syncing client: {$client->name} ({$client->category})");

        // 1. Resolve Path: /{Category}/{ClientName}/Berkas
        // Note: The structure in FileController is:
        // $safeCategoryName = preg_replace('/[^A-Za-z0-9 _-]/', '', $category);
        // $safeClientName = preg_replace('/[^A-Za-z0-9 _-]/', '', $client->name);
        // $drivePath = "{$safeCategoryName}/{$safeClientName}/Berkas";

        if (strtolower($client->category) === 'kantor narasumber hukum') {
            return; // Handled separately
        }

        $safeCategoryName = preg_replace('/[^A-Za-z0-9 _-]/', '', $client->category);
        $safeClientName = preg_replace('/[^A-Za-z0-9 _-]/', '', $client->name);

        // We need to FIND these folders to get their IDs.

        // Root -> Category
        $rootId = config('services.google.drive_folder_id', env('GOOGLE_DRIVE_FOLDER_ID'));
        $categoryId = $this->driveService->findFolderByName($safeCategoryName, $rootId);

        if (!$categoryId) {
            $this->warn("  Category folder not found in Drive: {$safeCategoryName}");
            return;
        }

        // Category -> Client
        $clientId = $this->driveService->findFolderByName($safeClientName, $categoryId);
        if (!$clientId) {
            $this->warn("  Client folder not found in Drive: {$safeClientName}");
            return;
        }

        // Client -> Berkas
        $berkasId = $this->driveService->findFolderByName('Berkas', $clientId);
        if (!$berkasId) {
            $this->warn("  'Berkas' folder not found in Drive for client: {$client->name}");
            // Optional: Create it if we want to enforce structure, but for sync we just skip
            // Or maybe look for files directly in Client folder? Implementation plan said /Berkas
            return;
        }

        // Now list folders inside 'Berkas'. These are the 'descriptions'.
        // e.g. /Berkas/Surat Menyurat/file.pdf
        // If files are directly in /Berkas, description is null.

        $this->syncFolderContents($berkasId, $client->id, null); // Files directly in Berkas

        $items = $this->driveService->listFiles($berkasId);
        foreach ($items as $item) {
            if ($item['mimeType'] === 'application/vnd.google-apps.folder') {
                // This is a "Description" folder
                $description = $item['name'];
                $this->info("    Scanning folder: {$description}");
                $this->syncFolderContents($item['id'], $client->id, $description);
            }
        }
    }

    private function syncPublicCategory($categoryName)
    {
        $this->info("Syncing public category: {$categoryName}");

        // Find or create the dummy client
        $client = Client::firstOrCreate(
            ['category' => $categoryName, 'name' => $categoryName],
            ['status' => 'active']
        );

        $safeCategoryName = preg_replace('/[^A-Za-z0-9 _-]/', '', $categoryName);
        $rootId = config('services.google.drive_folder_id', env('GOOGLE_DRIVE_FOLDER_ID'));
        $categoryId = $this->driveService->findFolderByName($safeCategoryName, $rootId);

        if (!$categoryId) {
            $this->warn("  Category folder not found: {$safeCategoryName}");
            return;
        }

        // For public category, folders inside Category ARE the descriptions.
        // /{Category}/{FolderName}/{Description} -> logic in FileController
        // $folderName = $description ?: 'Umum';
        // $drivePath = "{$safeCategoryName}/{$safeFolderName}";

        $items = $this->driveService->listFiles($categoryId);
        foreach ($items as $item) {
            if ($item['mimeType'] === 'application/vnd.google-apps.folder') {
                $description = $item['name'];
                $this->info("    Scanning public folder: {$description}");
                $this->syncFolderContents($item['id'], $client->id, $description);
            }
        }
    }

    private function syncFolderContents($folderId, $clientId, $description)
    {
        $files = $this->driveService->listFiles($folderId);
        $count = 0;

        foreach ($files as $driveFile) {
            if ($driveFile['mimeType'] === 'application/vnd.google-apps.folder') {
                continue; // Skip nested folders for now (not supported by DB schema depth)
            }

            // Check if file already exists in DB
            $exists = File::where('drive_file_id', $driveFile['id'])->exists();

            if (!$exists) {
                File::create([
                    'client_id' => $clientId,
                    'uploaded_by' => 1, // Admin (assuming ID 1)
                    'name' => $driveFile['name'],
                    'mime_type' => $driveFile['mimeType'],
                    'size' => $driveFile['size'],
                    'drive_file_id' => $driveFile['id'],
                    'description' => $description,
                    'created_at' => now(), // Or fetch createdTime from Drive if needed
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            $this->info("      Synced {$count} new files.");
        }
    }
}
