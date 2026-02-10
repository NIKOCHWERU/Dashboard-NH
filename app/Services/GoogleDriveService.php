<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $service;
    protected $folderId;

    public function __construct()
    {
        $client = new Client();
        
        $tokenPath = storage_path('app/google-drive-token.json');

        if (file_exists($tokenPath)) {
            // Priority: Use the Stored User Token (Admin's Token)
            // This allows using the 100GB+ storage of the real user account
            $tokenData = json_decode(file_get_contents($tokenPath), true);
            
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->refreshToken($tokenData['refresh_token']);
        } else {
            // Fallback: Service Account (Limited to 15GB usually)
            $client->setAuthConfig(storage_path('app/google-drive-service-account.json'));
        }

        $client->addScope(Drive::DRIVE);
        
        $this->service = new Drive($client);
        $this->folderId = config('services.google.drive_folder_id', env('GOOGLE_DRIVE_FOLDER_ID'));
    }

    /**
     * Upload a file to Google Drive
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string|null $folderId
     * @return string File ID
     */
    public function uploadFile($file, $folderId = null)
    {
        $folderId = $folderId ?: $this->folderId;
        
        $fileMetadata = new DriveFile([
            'name' => $file->getClientOriginalName(),
            'parents' => [$folderId]
        ]);

        $this->service->getClient()->setDefer(true);
        $request = $this->service->files->create($fileMetadata);
        
        // chunk size 10MB (must be multiple of 256KB)
        $chunkSizeBytes = 10 * 1024 * 1024; 
        
        $media = new \Google\Http\MediaFileUpload(
            $this->service->getClient(),
            $request,
            $file->getMimeType(),
            null,
            true, // Resumable
            $chunkSizeBytes
        );
        
        $fileSize = $file->getSize();
        $media->setFileSize($fileSize);

        $status = false;
        $handle = fopen($file->getRealPath(), "rb");
        
        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }
        
        // Reset defer to false so subsequent calls work normally
        $this->service->getClient()->setDefer(false);
        fclose($handle);

        if ($status instanceof DriveFile || (is_object($status) && isset($status->id))) {
            return $status->id;
        }

        throw new \Exception("Upload to Google Drive failed or returned invalid response.");
    }

    /**
     * Get file stream for downloading
     * 
     * @param string $fileId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getFileStream($fileId)
    {
        // Ensure defer is off for downloads
        $this->service->getClient()->setDefer(false);
        $response = $this->service->files->get($fileId, ['alt' => 'media']);
        return $response;
    }

    /**
     * Get Google Drive Web View Link
     * 
     * @param string $fileId
     * @return string
     */
    public function getFileWebLink($fileId)
    {
        $file = $this->service->files->get($fileId, ['fields' => 'webViewLink']);
        return $file->webViewLink;
    }

    /**
     * Get Google Drive Thumbnail URL
     * 
     * @param string $fileId
     * @return string|null
     */
    public function getThumbnailUrl($fileId)
    {
        try {
            $file = $this->service->files->get($fileId, ['fields' => 'thumbnailLink']);
            return $file->thumbnailLink ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Delete a file from Google Drive
     * 
     * @param string $fileId
     * @return void
     */
    public function deleteFile($fileId)
    {
        try {
            $this->service->files->delete($fileId);
        } catch (\Exception $e) {
            Log::error("Failed to delete file from Drive: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get exact usage of the target folder by summing file sizes.
     * Use this to reflect actual "Drive" usage for the archive.
     */
    public function getTotalFolderSize()
    {
        $size = 0;
        $pageToken = null;

        do {
            $response = $this->service->files->listFiles([
                'q' => "'{$this->folderId}' in parents and trashed = false",
                'fields' => 'nextPageToken, files(size)',
                'pageToken' => $pageToken
            ]);

            foreach ($response->files as $file) {
                $size += (int) $file->size;
            }

            $pageToken = $response->nextPageToken;
        } while ($pageToken != null);

        return $size;
    }

    /**
     * Get Storage Quota
     * 
     * @return array
     */
    public function getStorageQuota()
    {
        // Try to get Service Account quota first
        try {
            $about = $this->service->about->get(['fields' => 'storageQuota']);
            return [
                'limit' => (float) $about->storageQuota->limit,
                'usage' => (float) $about->storageQuota->usage,
            ];
        } catch (\Exception $e) {
            return ['limit' => 0, 'usage' => 0];
        }
    }

    /**
     * Find a folder by name within a parent folder.
     */
    public function findFolderByName($name, $parentId)
    {
        $query = "mimeType='application/vnd.google-apps.folder' and name = '{$name}' and '{$parentId}' in parents and trashed = false";
        $response = $this->service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
        ]);

        if (count($response->files) > 0) {
            return $response->files[0]->id;
        }

        return null;
    }

    /**
     * Create a new folder.
     */
    public function createFolder($name, $parentId)
    {
        $fileMetadata = new DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId]
        ]);

        $file = $this->service->files->create($fileMetadata, ['fields' => 'id']);
        return $file->id;
    }

    /**
     * Ensure a full path of folders exists, creating them if necessary.
     * Path example: "OfficeApp/Retainer/ClientName/Berkas"
     */
    public function ensureFolderExists($path)
    {
        $parts = array_filter(explode('/', $path));
        $parentId = $this->folderId; // Start from root folder ID configured in .env

        // If the path starts with OfficeApp, we treat the configured folder AS the root, 
        // or effectively "OfficeApp" is the root. 
        // Let's assume the configured GOOGLE_DRIVE_FOLDER_ID IS the "OfficeApp" folder 
        // OR we create "OfficeApp" inside it.
        // For safety, let's create inside the configured root.

        foreach ($parts as $part) {
            $foundId = $this->findFolderByName($part, $parentId);
            
            if ($foundId) {
                $parentId = $foundId;
            } else {
                $parentId = $this->createFolder($part, $parentId);
            }
        }

        return $parentId;
    }

    /**
     * Ensure a category folder exists and return its ID.
     * Creates the folder if it doesn't exist.
     * 
     * @param string $categoryName
     * @return string Folder ID
     */
    public function ensureCategoryFolder($categoryName)
    {
        // Sanitize category name for folder usage
        $safeCategoryName = preg_replace('/[^A-Za-z0-9 _-]/', '', $categoryName);
        
        // Check if folder exists
        $categoryFolderId = $this->findFolderByName($safeCategoryName, $this->folderId);
        
        // Create if doesn't exist
        if (!$categoryFolderId) {
            $categoryFolderId = $this->createFolder($safeCategoryName, $this->folderId);
        }
        
        return $categoryFolderId;
    }
}
