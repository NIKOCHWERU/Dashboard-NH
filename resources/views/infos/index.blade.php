@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold">Manajemen Info & Pengumuman</h2>
    <button onclick="document.getElementById('infoModal').classList.remove('hidden')" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded">
        + Tambah Info
    </button>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="p-6">
        <div class="space-y-4">
            @forelse($infos as $info)
            <div class="border rounded-lg p-4 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">{{ $info->title }}</h3>
                            @if($info->is_public)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Public</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Private</span>
                            @endif
                        </div>
                        <p class="text-gray-700 mb-2">{{ $info->content }}</p>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>By {{ $info->creator->name }}</span>
                            <span>{{ $info->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <form action="{{ route('infos.destroy', $info) }}" method="POST" onsubmit="return confirm('Hapus info?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm ml-4">Hapus</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-8">Belum ada info.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal -->
<div id="infoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <h3 class="text-xl font-bold mb-4">Tambah Info Baru</h3>
        <form action="{{ route('infos.store') }}" method="POST">
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
@endsection
