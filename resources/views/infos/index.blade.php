@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold">Manajemen Info & Pengumuman</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi dan pengumuman untuk tim</p>
    </div>
    <button onclick="document.getElementById('infoModal').classList.remove('hidden')" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-sm transition">
        + Tambah Info
    </button>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($infos as $info)
            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                @if($info->image)
                <div class="mb-3 rounded-lg overflow-hidden">
                    <img src="{{ asset('storage/' . $info->image) }}" alt="{{ $info->title }}" class="w-full h-48 object-cover">
                </div>
                @endif
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">{{ $info->title }}</h3>
                            @if($info->is_public)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Public</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Private</span>
                            @endif
                        </div>
                        <p class="text-gray-700 mb-2 text-sm">{{ Str::limit($info->content, 150) }}</p>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $info->creator->name }}
                            </span>
                            <span>{{ $info->created_at->diffForHumans() }}</span>
                            @if($info->expires_at)
                            <span class="flex items-center gap-1 text-orange-600 font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Sampai {{ $info->expires_at->format('d M Y') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('infos.destroy', $info) }}" method="POST" onsubmit="return confirm('Hapus info?');" class="ml-2">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm p-2 hover:bg-red-50 rounded transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-2">
                <p class="text-center text-gray-500 py-8">Belum ada info.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal -->
<div id="infoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Tambah Info Baru</h3>
        <form action="{{ route('infos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Judul *</label>
                    <input type="text" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Konten *</label>
                    <textarea name="content" required rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar (Opsional)</label>
                    <input type="file" name="image" accept="image/*" id="imageInput" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                    <p class="mt-1 text-xs text-gray-400">PNG, JPG, GIF max 5MB</p>
                    <div id="imagePreview" class="mt-3 hidden">
                        <img id="previewImg" src="" alt="Preview" class="max-w-full h-48 rounded-lg border border-gray-200">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Kadaluarsa (Opsional)</label>
                    <input type="date" name="expires_at" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2 border">
                    <p class="mt-1 text-xs text-gray-400">Info akan otomatis disembunyikan setelah tanggal ini</p>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_public" id="is_public" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                    <label for="is_public" class="ml-2 text-sm text-gray-700">Tampilkan di Dashboard (Public)</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview
document.getElementById('imageInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagePreview').classList.add('hidden');
    }
});
</script>
@endsection
