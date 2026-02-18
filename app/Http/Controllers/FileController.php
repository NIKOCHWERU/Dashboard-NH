<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\File;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    protected $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // --- 1. Navigation Logic ---

        // Level 3: Files List (Final Depth)
        if ($request->has('client_id') && $request->has('folder')) {
            $viewMode = 'files';
            $client = Client::findOrFail($request->client_id);
            $folderName = $request->folder;

            $query = File::with(['uploader'])
                ->where('client_id', $client->id)
                ->where('description', $folderName)
                ->latest();

            $items = $query->paginate(50);

            $breadcrumbs = [
                ['label' => 'Home', 'url' => route('files.index')],
                ['label' => ucfirst($client->category), 'url' => route('files.index', ['category' => $client->category])],
                ['label' => $client->name, 'url' => route('files.index', ['client_id' => $client->id])],
                ['label' => $folderName, 'url' => '#'],
            ];

            return view('files.index', compact('viewMode', 'items', 'breadcrumbs', 'client', 'folderName'));
        }

        // Level 2: Folders (Descriptions) List for a Client
        if ($request->has('client_id')) {
            $viewMode = 'folders';
            $client = Client::findOrFail($request->client_id);

            // Authorization Check
            if (!$user->isAdmin() && !$user->clients()->where('clients.id', $client->id)->exists()) {
                abort(403, 'Unauthorized access to this client.');
            }

            // Get unique descriptions (Folders)
            $items = File::where('client_id', $client->id)
                ->select('description', DB::raw('count(*) as count'))
                ->groupBy('description')
                ->orderBy('description')
                ->get();

            if (strtolower($client->category) === 'kantor narasumber hukum') {
                $breadcrumbs = [
                    ['label' => 'Home', 'url' => route('files.index')],
                    ['label' => 'Kantor Narasumber Hukum', 'url' => '#'],
                ];
            } else {
                $breadcrumbs = [
                    ['label' => 'Home', 'url' => route('files.index')],
                    ['label' => ucfirst($client->category), 'url' => route('files.index', ['category' => $client->category])],
                    ['label' => $client->name, 'url' => '#'],
                ];
            }

            // Also get clients for the upload modal (dropdown)
            $uploadClients = $user->isAdmin() ? Client::all() : $user->clients;
            // Get all unique descriptions for the datalist suggestions
            $suggestions = File::select('description')->distinct()->pluck('description');

            return view('files.index', compact('viewMode', 'items', 'breadcrumbs', 'client', 'uploadClients', 'suggestions'));
        }

        // Level 1: Clients List for a Category
        if ($request->has('category')) {
            $category = $request->category;

            // Special handling for public category "Kantor Narasumber Hukum"
            if (strtolower($category) === 'kantor narasumber hukum') {
                // Find or create a dummy client for this category
                $client = Client::firstOrCreate(
                    ['category' => $category, 'name' => 'Kantor Narasumber Hukum'],
                    ['status' => 'active']
                );

                // Redirect to folders view (which shows upload button)
                return redirect()->route('files.index', ['client_id' => $client->id]);
            }

            $viewMode = 'clients';

            $query = Client::where('category', $category);

            // Filter by assignment if not admin
            if (!$user->isAdmin()) {
                $query->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            }

            $items = $query->withCount('files')->get();

            $breadcrumbs = [
                ['label' => 'Home', 'url' => route('files.index')],
                ['label' => ucfirst($category), 'url' => '#'],
            ];

            return view('files.index', compact('viewMode', 'items', 'breadcrumbs', 'category'));
        }

        // Level 0: Root (Categories)
        $viewMode = 'categories';

        $fixedCategories = ['Retainer', 'Perorangan', 'Kantor Narasumber Hukum'];
        $dbCategories = Client::select('category')->distinct()->pluck('category')->toArray();

        // Merge
        $all = array_merge($fixedCategories, $dbCategories);

        // Normalize everything to Title Case (ucfirst) to deduplicate 'retainer' vs 'Retainer'
        $allNormalized = array_map(function ($c) {
            return ucfirst($c);
        }, $all);

        $items = array_unique($allNormalized);

        $breadcrumbs = [];

        // Fetch recent files for this view
        $recentFiles = File::with(['client', 'uploader'])->latest()->take(10)->get();

        return view('files.index', compact('viewMode', 'items', 'breadcrumbs', 'recentFiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'files' => 'required|array|max:100', // Max 100 files
            'files.*' => 'required|file|max:1048576', // Max 1GB (1048576 KB)
            'description' => 'nullable|string|max:255',
        ]);

        $clientId = $request->client_id;
        $description = $request->description;

        // Check permission if user is not admin
        // Skip check for "Kantor Narasumber Hukum" - public category
        $client = Client::find($clientId);

        if (!auth()->user()->isAdmin() && strtolower($client->category) !== 'kantor narasumber hukum') {
            if (!auth()->user()->clients()->where('clients.id', $clientId)->exists()) {
                abort(403, 'You do not have access to this client.');
            }
        }

        // Determine Target Drive Path with Category Folders
        // New structure: /{Category}/{ClientName}/Berkas/{Description}
        // For "Kantor Narasumber Hukum": /{Category}/{FolderName}/{Description}

        $category = $client->category;
        $safeCategoryName = preg_replace('/[^A-Za-z0-9 _-]/', '', $category);

        // Ensure category folder exists
        $categoryFolderId = $this->driveService->ensureCategoryFolder($category);

        // Build path based on category
        if (strtolower($category) === 'kantor narasumber hukum') {
            // Public category: use folder name directly (description becomes folder name)
            $folderName = $description ?: 'Umum';
            $safeFolderName = preg_replace('/[^A-Za-z0-9 _-]/', '', $folderName);
            $drivePath = "{$safeCategoryName}/{$safeFolderName}";
        } else {
            // Regular categories: use client name
            $safeClientName = preg_replace('/[^A-Za-z0-9 _-]/', '', $client->name);
            $drivePath = "{$safeCategoryName}/{$safeClientName}/Berkas";

            if ($description) {
                $drivePath .= "/" . preg_replace('/[^A-Za-z0-9 _-]/', '', $description);
            }
        }

        $uploadedCount = 0;

        DB::beginTransaction();
        try {
            // Ensure folder exists recursively
            $targetFolderId = $this->driveService->ensureFolderExists($drivePath);

            foreach ($request->file('files') as $file) {
                // Upload to Drive (Streamed) with specific folder ID
                $driveFileId = $this->driveService->uploadFile($file, $targetFolderId);

                // Save to DB
                File::create([
                    'client_id' => $clientId,
                    'uploaded_by' => auth()->id(),
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'drive_file_id' => $driveFileId,
                    'description' => $description,
                ]);

                $uploadedCount++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Upload failed for client ' . $clientId . ': ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "$uploadedCount files uploaded successfully.",
                'count' => $uploadedCount
            ], 200);
        }

        return redirect()->route('files.index')->with('success', "$uploadedCount files uploaded successfully.");
    }

    public function view(File $file)
    {
        $this->authorizeFileAccess($file);

        try {
            $url = $this->driveService->getFileWebLink($file->drive_file_id);
            return redirect()->away($url);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Could not open file in Drive: ' . $e->getMessage());
        }
    }

    public function download(File $file)
    {
        $this->authorizeFileAccess($file);

        // Log download
        Log::info('File downloaded: ' . $file->id . ' by User: ' . auth()->id());

        $response = $this->driveService->getFileStream($file->drive_file_id);

        // If response is a Guzzle Response, get the body stream
        $stream = ($response instanceof \Psr\Http\Message\ResponseInterface) ? $response->getBody() : $response;

        return response()->streamDownload(function () use ($stream) {
            while (!$stream->eof()) {
                echo $stream->read(1024 * 8);
            }
        }, $file->name, ['Content-Type' => $file->mime_type]);
    }

    public function bulkDownload(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'required|exists:files,id',
        ]);

        $files = File::whereIn('id', $request->file_ids)->get();

        // Authorization check
        foreach ($files as $file) {
            $this->authorizeFileAccess($file);
        }

        // Create ZIP file
        $zipFileName = 'files_' . date('Y-m-d_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Could not create ZIP file.');
        }

        try {
            foreach ($files as $file) {
                $response = $this->driveService->getFileStream($file->drive_file_id);
                $stream = ($response instanceof \Psr\Http\Message\ResponseInterface) ? $response->getBody() : $response;

                // Add file to ZIP
                $zip->addFromString($file->name, $stream->getContents());
            }

            $zip->close();

            // Return ZIP and delete after sending
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            $zip->close();
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            Log::error('Bulk download failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Bulk download failed: ' . $e->getMessage());
        }
    }

    public function destroy(File $file)
    {
        $this->authorizeFileAccess($file);

        // Admin only or owner/uploader? Requirement says "Admin: kelola user, klien, semua file", "User: upload & lihat". 
        // Assuming Users shouldn't delete, or maybe they can? Safest is Admin only or Uploader.
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admin can delete files.');
        }

        try {
            try {
                $this->driveService->deleteFile($file->drive_file_id);
            } catch (\Exception $e) {
                Log::warning('File missing from Drive or delete failed, proceeding to DB delete: ' . $e->getMessage());
            }
            $file->delete();
            return redirect()->back()->with('success', 'File deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    public function destroyFolder(Request $request)
    {
        // Admin only
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admin can delete folders.');
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'folder' => 'nullable|string',
        ]);

        $clientId = $request->client_id;
        $folderName = $request->folder;

        // Find all files in this folder
        $query = File::where('client_id', $clientId);

        if (empty($folderName)) {
            $query->whereNull('description');
        } else {
            $query->where('description', $folderName);
        }

        $files = $query->get();

        if ($files->isEmpty()) {
            return redirect()->back()->with('error', 'Folder not found or already empty.');
        }

        $fileCount = $files->count();

        DB::beginTransaction();
        try {
            foreach ($files as $file) {
                // Delete from Google Drive
                try {
                    $this->driveService->deleteFile($file->drive_file_id);
                } catch (\Exception $e) {
                    Log::warning('File missing from Drive or delete failed during folder delete: ' . $e->getMessage());
                }
                // Delete from database
                $file->delete();
            }
            DB::commit();

            return redirect()->route('files.index', ['client_id' => $clientId])
                ->with('success', "Folder '$folderName' and $fileCount file(s) deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Folder deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Folder deletion failed: ' . $e->getMessage());
        }
    }

    public function getFolderLinks(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'folder' => 'nullable|string',
        ]);

        $clientId = $request->client_id;
        $folderName = $request->folder;

        $user = auth()->user();
        if (!$user->isAdmin()) {
            // Basic check: user belongs to client
            if (!$user->clients()->where('clients.id', $clientId)->exists()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $query = File::where('client_id', $clientId);

        if (empty($folderName)) {
            $query->whereNull('description');
        } else {
            $query->where('description', $folderName);
        }

        $files = $query->get(['name', 'drive_file_id', 'mime_type']);

        $links = $files->map(function ($file) {
            return [
                'name' => $file->name,
                'link' => "https://drive.google.com/file/d/{$file->drive_file_id}/view?usp=sharing",
                'is_image' => str_starts_with($file->mime_type, 'image/') ? true : false,
            ];
        });

        return response()->json([
            'files' => $links,
            'count' => $files->count(),
            'folder' => $folderName ?: 'Tanpa Keterangan'
        ]);
    }

    private function authorizeFileAccess(File $file)
    {
        $user = auth()->user();
        if ($user->isAdmin())
            return true;

        if (!$user->clients()->where('clients.id', $file->client_id)->exists()) {
            abort(403, 'Unauthorized access to this file.');
        }
    }
}
