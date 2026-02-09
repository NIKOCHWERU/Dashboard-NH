<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\GoogleDriveService;

class ProfileController extends Controller
{
    protected $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:2048'], // Max 2MB for avatar
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $request->validate(['password' => 'confirmed|min:8']);
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            try {
                // Determine Folder Path
                $targetFolderId = $this->driveService->ensureFolderExists('OfficeApp/Profiles');
                
                // Upload
                $fileId = $this->driveService->uploadFile($request->file('avatar'), $targetFolderId);
                
                // Get Thumbnail/View Link
                $avatarUrl = $this->driveService->getFileWebLink($fileId);
                
                // Hack: Google Drive View Link requires workaround for direct image usage in <img> tags sometimes, 
                // but we will store the File ID or the Thumbnail Link. 
                // Creating a thumbnail link manually or using the service helper.
                $thumbnail = $this->driveService->getThumbnailUrl($fileId);

                $user->avatar = $thumbnail ?? $avatarUrl; // Prefer thumbnail for avatar
                
            } catch (\Exception $e) {
                return back()->with('error', 'Avatar upload failed: ' . $e->getMessage());
            }
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
