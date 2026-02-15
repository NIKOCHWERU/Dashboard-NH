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

        // 1. Sync Public Category
        $this->syncPublicCategory('Kantor Narasumber Hukum');

        // 2. Discover & Sync Clients (Retainer & Perorangan)
        if (!$clientId) {
            $categories = ['Retainer', 'Perorangan'];
            foreach ($categories as $category) {
                $this->discoverAndSyncClients($category);
            }
        } else {
            // If specific client requested, just sync that one
            $client = Client::find($clientId);
            if ($client) {
                $this->syncClientFiles($client);
            } else {
                $this->error("Client with ID $clientId not found.");
            }
        }

        $this->info('Sync completed.');
    }

    private function discoverAndSyncClients($categoryName)
    {
        $this->info("Scanning category: {$categoryName}");

        $rootId = config('services.google.drive_folder_id', env('GOOGLE_DRIVE_FOLDER_ID'));
        // Find category folder
        // Note: Logic in FileController uses a "Safe Name", e.g. "Retainer", "Perorangan"
        // We assume the folder name in Drive matches the Category Name exactly or close enough.

        $safeCategoryName = preg_replace('/[^A-Za-z0-9 _-]/', '', $categoryName);
        $categoryId = $this->driveService->findFolderByName($safeCategoryName, $rootId);

        if (!$categoryId) {
            $this->warn("  Category folder not found in Drive: {$safeCategoryName}");
            return;
        }

        // List all folders inside this Category Folder -> These are CLIENTS
        $items = $this->driveService->listFiles($categoryId);

        foreach ($items as $item) {
            if ($item['mimeType'] === 'application/vnd.google-apps.folder') {
                $clientName = $item['name'];
                $this->info("  Found Client Folder: {$clientName}");

                // Check if Client exists in DB
                $client = Client::where('name', $clientName)
                    ->where('category', $categoryName)
                    ->first();

                if (!$client) {
                    $this->info("    Creating new client record: {$clientName}");
                    $client = Client::create([
                        'name' => $clientName,
                        'category' => $categoryName,
                        'status' => 'active', // Default status
                        // 'retainer_uuid' ?? 'address' ??
                    ]);
                }

                // Now Sync Files for this client
                // We pass the KNOWN client folder ID to avoid searching for it again
                $this->syncClientFiles($client, $item['id']);
            }
        }
    }

    private function syncClientFiles(Client $client, $knownClientFolderId = null)
    {
        $this->info("    Syncing files for: {$client->name}");

        if (strtolower($client->category) === 'kantor narasumber hukum') {
            return; // Handled separately
        }

        $clientId = $knownClientFolderId;

        // If we don't know the ID (e.g. run via --client option), find it
        if (!$clientId) {
            $safeCategoryName = preg_replace('/[^A-Za-z0-9 _-]/', '', $client->category);
            $safeClientName = preg_replace('/[^A-Za-z0-9 _-]/', '', $client->name);

            $rootId = config('services.google.drive_folder_id', env('GOOGLE_DRIVE_FOLDER_ID'));
            $categoryId = $this->driveService->findFolderByName($safeCategoryName, $rootId);

            if (!$categoryId) {
                // If category doesn't exist, client folder can't exist
                $this->warn("      Category folder not found in Drive: {$safeCategoryName}");
                return;
            }

            $clientId = $this->driveService->findFolderByName($safeClientName, $categoryId);
        }

        if (!$clientId) {
            $this->warn("      Client folder not found in Drive.");
            return;
        }

        // Client -> Berkas
        $berkasId = $this->driveService->findFolderByName('Berkas', $clientId);

        // If /Berkas exists, we look there. 
        // If NOT, maybe files are just in the Client Root? 
        // Logic in FileController ENFORCES /Berkas, so we should probably stick to that locally,
        // BUT for import, if user manually put files in Client Root, we might want to see them?
        // Let's stick to FileController logic: $drivePath = "{$safeCategoryName}/{$safeClientName}/Berkas";
        // So yes, we expect 'Berkas'.

        if (!$berkasId) {
            $this->warn("      'Berkas' folder not found. Scanning root of client folder instead?");
            // Fallback: Scan root of client folder, treating files as 'Umum' (null description)
            // and folders as descriptions.
            $berkasId = $clientId;
        }

        $this->syncFolderContents($berkasId, $client->id, null); // Files directly in Berkas/Root

        $items = $this->driveService->listFiles($berkasId);
        foreach ($items as $item) {
            if ($item['mimeType'] === 'application/vnd.google-apps.folder') {
                // This is a "Description" folder (e.g. "Surat Menyurat")
                // UNLESS we are scanning root and found "Berkas" (recursive issue? no we just set berkasId)
                if ($berkasId === $clientId && $item['name'] === 'Berkas') {
                    continue; // Avoid double scanning if we fell back to root
                }

                $description = $item['name'];
                $this->info("      -> Folder: {$description}");
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
