@extends('layouts.app')

@section('content')

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Manajemen Berkas</h2>
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('files.index') }}"
                            class="text-sm font-medium text-gray-500 hover:text-primary transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                            Home
                        </a>
                    </li>
                    @if(!empty($breadcrumbs))
                        @foreach($breadcrumbs as $crumb)
                            @if(!$loop->first) {{-- Skip 'Home' if it's already in the $breadcrumbs list to avoid duplication --}}
                                @if($crumb['label'] !== 'Home')
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        @if($crumb['url'] != '#' && !$loop->last)
                                            <a href="{{ $crumb['url'] }}"
                                                class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">{{ $crumb['label'] }}</a>
                                        @else
                                            <span class="text-sm font-bold text-gray-900">{{ $crumb['label'] }}</span>
                                        @endif
                                    </li>
                                @endif
                            @endif
                        @endforeach
                    @endif
                </ol>
            </nav>
        </div>

        <!-- Action Buttons (Search & Upload) -->
        <div class="flex items-center gap-3">
            @if(isset($viewMode) && $viewMode == 'files')
                <form action="{{ url()->current() }}" method="GET" class="relative group">
                    @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berkas..."
                        class="w-48 md:w-64 pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
                    <div
                        class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>
            @endif

            @if(isset($viewMode) && ($viewMode == 'folders' || $viewMode == 'files'))
                <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                    class="inline-flex items-center px-4 py-2.5 bg-primary hover:bg-primary-hover text-white rounded-xl shadow-lg shadow-primary/20 transition-all font-bold text-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Unggah Berkas Baru
                </button>
            @endif
        </div>
    </div>


    <!-- Checks server limits for UI rendering -->
    @php
        function parseSize($size)
        {
            $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
            $size = preg_replace('/[^0-9\.]/', '', $size);
            if ($unit) {
                return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
            }
            return round($size);
        }
        $maxPostSize = parseSize(ini_get('post_max_size'));
    @endphp

    <!-- VIEW MODE 0: CATEGORIES -->
    @if($viewMode == 'categories')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Recent Files (Span 2) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="p-2 bg-primary/10 text-primary rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                        Berkas Terbaru
                    </h3>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Berkas</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Klien</th>
                                    <th
                                        class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentFiles as $file)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="p-2 bg-gray-50 rounded-lg text-gray-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-semibold text-gray-700 truncate max-w-[180px]"
                                                        title="{{ $file->name }}">{{ $file->name }}</span>
                                                    <span
                                                        class="text-[10px] text-gray-400 font-medium">{{ $file->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600 uppercase">{{ $file->client->name }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('files.download', $file) }}"
                                                class="p-2 text-gray-400 hover:text-primary transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic text-sm">Belum ada
                                            berkas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Categories -->
            <div class="space-y-6">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </span>
                    Kategori Utama
                </h3>

                <div class="flex flex-col gap-3">
                    @foreach($items as $cat)
                        <a href="{{ route('files.index', ['category' => $cat]) }}" class="group">
                            <div
                                class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-primary/20 transition-all flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="p-3 bg-gray-50 text-gray-400 group-hover:bg-primary/10 group-hover:text-primary rounded-xl transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">{{ ucfirst($cat) }}</h4>
                                        <p class="text-[10px] text-gray-500 font-medium">Buka Berkas &rarr;</p>
                                    </div>
                                </div>
                                <div class="text-gray-300 group-hover:text-primary transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    <!-- VIEW MODE 1: CLIENTS -->
    @if($viewMode == 'clients')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $client)
                <a href="{{ route('files.index', ['client_id' => $client->id]) }}" class="group">
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-primary/20 transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-primary group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-gray-900 group-hover:text-primary transition-colors text-sm">
                                        {{ $client->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $client->files_count }}
                                            Berkas</span>
                                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                        <span class="text-[10px] font-bold text-primary uppercase">Lihat Detail</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-gray-300 group-hover:text-primary group-hover:translate-x-1 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <!-- VIEW MODE 2: FOLDERS (DESCRIPTIONS) -->
    @if($viewMode == 'folders')
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @foreach($items as $folder)
                <div class="relative group">
                    <a href="{{ route('files.index', ['client_id' => $client->id, 'folder' => $folder->description]) }}"
                        class="block">
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-md hover:border-primary/20 transition-all group-hover:-translate-y-1">
                            <div
                                class="mx-auto w-16 h-16 mb-4 flex items-center justify-center bg-yellow-50 rounded-2xl text-yellow-500 group-hover:bg-yellow-100 transition-colors">
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                </svg>
                            </div>
                            <h5 class="font-bold text-gray-900 truncate px-2 text-sm mb-1" title="{{ $folder->description }}">
                                {{ $folder->description ?: 'Tanpa Keterangan' }}
                            </h5>
                            <span
                                class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-[10px] font-bold text-gray-500 rounded-lg group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                {{ $folder->count }} Berkas
                            </span>
                        </div>
                    </a>

                    <div
                        class="absolute top-2 right-2 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                        @if(auth()->user()->isAdmin())
                            <button
                                onclick="confirmDeleteFolder({{ json_encode($folder->description) }}, {{ $folder->count }}, {{ $client->id }})"
                                class="bg-white/80 backdrop-blur-sm text-red-500 hover:bg-red-500 hover:text-white p-2 rounded-xl shadow-sm border border-red-100 transition-all"
                                title="Hapus Folder">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        @endif
                        <button onclick="emailFolder({{ $client->id }}, {{ json_encode($folder->description) }})"
                            class="bg-white/80 backdrop-blur-sm text-blue-600 hover:bg-blue-600 hover:text-white p-2 rounded-xl shadow-sm border border-blue-100 transition-all"
                            title="Email Link Folder">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>


        <!-- Delete Folder Modal -->
        <div id="deleteFolderModal"
            class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[70] backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Folder Keterangan?</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Anda akan menghapus folder <span id="delFolderName" class="font-bold text-gray-800"></span> yang berisi
                        <span id="delFileCount" class="font-bold text-gray-800"></span> berkas. <br>
                        <span class="text-red-600 font-semibold italic">Tindakan ini tidak dapat dibatalkan dan semua berkas di
                            Drive akan terhapus.</span>
                    </p>
                    <form id="deleteFolderForm" action="{{ route('files.destroyFolder') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="client_id" id="delClientId">
                        <input type="hidden" name="folder" id="delFolderNameInput">
                        <div class="flex flex-col gap-3">
                            <button type="submit"
                                class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition-all">
                                Ya, Hapus Semua
                            </button>
                            <button type="button" onclick="closeDeleteFolderModal()"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function confirmDeleteFolder(folderName, count, clientId) {
                // folderName can be empty string for 'No Description'
                document.getElementById('delFolderName').textContent = folderName || 'Tanpa Keterangan';
                document.getElementById('delFileCount').textContent = count;
                document.getElementById('delClientId').value = clientId;
                document.getElementById('delFolderNameInput').value = folderName || '';
                document.getElementById('deleteFolderModal').classList.remove('hidden');
            }

            function closeDeleteFolderModal() {
                document.getElementById('deleteFolderModal').classList.add('hidden');
            }
        </script>
        @if($items->isEmpty())
            <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada Folder</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai upload file untuk membuat folder keterangan baru.</p>
                <div class="mt-6">
                    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Upload File Baru
                    </button>
                </div>
            </div>
        @endif


        // Highlight drop zone
        ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
        dropZone.classList.add('border-primary', 'bg-blue-50');
        }
        function unhighlight(e) {
        dropZone.classList.remove('border-primary', 'bg-blue-50');
        }

        // Handle dropped files
        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        fileInput.files = files;
        const event = new Event('change');
        fileInput.dispatchEvent(event);
        }

        fileInput.addEventListener('change', function () {
        fileList.innerHTML = '';
        if (this.files.length > 0) {
        let totalSize = 0;
        for (let i = 0; i < this.files.length; i++) { totalSize +=this.files[i].size; } const formatSize=(bytes)=> {
            if (bytes < 1024) return bytes + ' B' ; if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB' ; if
                (bytes < 1024 * 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(2) + ' MB' ; return (bytes / (1024 * 1024 *
                1024)).toFixed(2) + ' GB' ; }; const summary=document.createElement('div');
                summary.className='mb-2 p-2 bg-blue-50 border border-blue-200 rounded-lg sticky top-0 z-10' ; // Sticky summary
                summary.innerHTML=` <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-blue-800">📁 ${this.files.length} file dipilih</span>
                <span class="font-semibold text-blue-600">Total: ${formatSize(totalSize)}</span>
                </div>
                `;
                fileList.appendChild(summary);

                Array.from(this.files).forEach((file, index) => {
                const isImage = file.type.startsWith('image/');
                const div = document.createElement('div');
                div.className = 'text-[10px] text-gray-500 flex items-center gap-2 p-1 hover:bg-gray-50 rounded border-b
                border-gray-50 last:border-0';

                let icon = `<svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                    </path>
                </svg>`;

                if (isImage) {
                const url = URL.createObjectURL(file);
                icon = `<img src="${url}" class="w-6 h-6 object-cover rounded border border-gray-200">`;
                }

                div.innerHTML = `${icon} <span class="truncate flex-1">${file.name}</span> <span
                    class="text-xs text-gray-300 whitespace-nowrap">${formatSize(file.size)}</span>`;
                fileList.appendChild(div);
                });
                }
                });

                // AJAX Upload Handler
                form.addEventListener('submit', function (e) {
                e.preventDefault();

                const files = fileInput.files;
                if (files.length === 0) return;

                uploadBtn.disabled = true;
                uploadBtn.innerText = 'Uploading...';
                const progressContainer = document.getElementById('progress-container');
                const progressList = document.getElementById('progress-list');
                progressContainer.classList.remove('hidden');
                progressList.innerHTML = '';

                const queue = Array.from(files);
                const totalFiles = queue.length;
                let completedCount = 0;
                let hasErrors = false;

                const processQueue = async () => {
                const overallStatus = document.getElementById('overall-status');
                const concurrencyLimit = 3;
                let activeUploads = 0;
                let currentIndex = 0;

                const next = async () => {
                if (currentIndex >= totalFiles) return;

                const i = currentIndex++;
                const file = queue[i];
                const progressId = 'prog-' + i;

                const item = document.createElement('div');
                item.className = 'text-[10px] text-gray-600 bg-gray-50 p-2 rounded-xl border border-gray-100 mb-2 animate-in
                slide-in-from-bottom-2 duration-300';
                item.innerHTML = `
                <div class="flex justify-between mb-1.5 px-1">
                    <span class="truncate w-2/3 font-bold">${file.name}</span>
                    <span id="${progressId}-status" class="font-bold text-primary">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1 overflow-hidden">
                    <div id="${progressId}-bar" class="bg-primary h-1 rounded-full transition-all duration-300"
                        style="width: 0%"></div>
                </div>
                `;
                progressList.appendChild(item);
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                activeUploads++;
                try {
                await uploadSingleFile(file, i, progressId);
                } catch (err) {
                hasErrors = true;
                } finally {
                activeUploads--;
                completedCount++;
                overallStatus.textContent = `${completedCount}/${totalFiles} Berhasil`;
                await next();
                }
                };

                const initialTasks = [];
                for (let i = 0; i < Math.min(concurrencyLimit, totalFiles); i++) { initialTasks.push(next()); } await
                    Promise.all(initialTasks); if (!hasErrors) { overallStatus.className='text-xs font-bold text-green-600' ;
                    overallStatus.textContent='Semua Berhasil Diunggah!' ; setTimeout(()=> {
                    window.location.reload();
                    }, 1500);
                    } else {
                    uploadBtn.disabled = false;
                    uploadBtn.innerText = 'Coba Lagi / Selesai';
                    overallStatus.className = 'text-xs font-bold text-red-600';
                    overallStatus.textContent = `${completedCount}/${totalFiles} Selesai (Beberapa Gagal)`;
                    alert('Beberapa berkas gagal diunggah. Silakan periksa status di daftar progress.');
                    }
                    };

                    const uploadSingleFile = (file, index, progressId) => {
                    return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    const clientId = form.querySelector('input[name="client_id"]').value;
                    const description = form.querySelector('input[name="description"]').value;

                    formData.append('client_id', clientId);
                    formData.append('description', description);
                    formData.append('files[]', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    const xhr = new XMLHttpRequest();
                    const progressBar = document.getElementById(progressId + '-bar');
                    const statusText = document.getElementById(progressId + '-status');

                    xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    statusText.innerText = percent + '%';
                    }
                    });

                    xhr.addEventListener('load', function () {
                    if (xhr.status >= 200 && xhr.status < 300) { progressBar.classList.replace('bg-primary', 'bg-green-500' );
                        statusText.innerText='Berhasil' ; statusText.className='font-bold text-green-600' ; resolve(); } else {
                        progressBar.classList.replace('bg-primary', 'bg-red-500' ); let errorMsg='Gagal' ; try { const
                        resp=JSON.parse(xhr.responseText); if (resp.error) errorMsg=resp.error; else if (resp.message)
                        errorMsg=resp.message; } catch (e) { } statusText.innerText=errorMsg;
                        statusText.className='font-bold text-red-600' ; reject(new Error(errorMsg)); } });
                        xhr.addEventListener('error', function () { statusText.innerText='Network Error' ;
                        statusText.className='font-bold text-red-600' ; reject(new Error('Network Error')); });
                        xhr.open('POST', '{{ route("files.store") }}' ); xhr.setRequestHeader('Accept', 'application/json' );
                        xhr.send(formData); }); }; processQueue(); }); </script>
                        </div>
                        </div>
    @endif

                    <!-- VIEW MODE 3: FILES LIST -->
                    @if($viewMode == 'files')
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <!-- Bulk Actions Toolbar -->
                            <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between bg-gray-50/50 gap-4"
                                id="bulk-actions" style="display: none;">
                                <div class="flex items-center gap-3">
                                    <span class="p-2 bg-primary/10 text-primary rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                    </span>
                                    <span class="text-sm font-bold text-gray-700"><span id="selected-count">0</span> file
                                        dipilih</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button onclick="downloadSelected()"
                                        class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-green-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download ZIP
                                    </button>
                                    <button onclick="emailLinks()"
                                        class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-blue-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Email Link
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-100">
                                    <thead class="bg-gray-50/50">
                                        <tr>
                                            <th class="px-6 py-4 text-left">
                                                <input type="checkbox" id="select-all"
                                                    class="rounded-md border-gray-300 text-primary focus:ring-primary h-4 w-4">
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                Nama
                                                Berkas</th>
                                            <th
                                                class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                Ukuran
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                Tanggal</th>
                                            <th
                                                class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($items as $file)
                                            <tr class="hover:bg-gray-50/30 transition-colors group cursor-default">
                                                <td class="px-6 py-4">
                                                    <input type="checkbox" name="selected_files[]" value="{{ $file->id }}"
                                                        data-file-name="{{ $file->name }}"
                                                        data-file-link="https://drive.google.com/file/d/{{ $file->drive_file_id }}/view?usp=sharing"
                                                        data-file-is-image="{{ str_starts_with($file->mime_type, 'image/') ? '1' : '0' }}"
                                                        class="file-checkbox rounded-md border-gray-300 text-primary focus:ring-primary h-4 w-4"
                                                        onchange="updateBulkUI()">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        @php
                                                            $isImage = str_starts_with($file->mime_type, 'image/');
                                                            $thumbnailUrl = $isImage ? "https://drive.google.com/thumbnail?id={$file->drive_file_id}&sz=w100" : null;
                                                        @endphp
                                                        <div class="relative w-10 h-10 flex-shrink-0">
                                                            @if($thumbnailUrl)
                                                                <img src="{{ $thumbnailUrl }}" alt="{{ $file->name }}"
                                                                    class="w-10 h-10 object-cover rounded-lg border border-gray-100 shadow-sm"
                                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                <div
                                                                    class="hidden absolute inset-0 bg-gray-50 rounded-lg items-center justify-center text-gray-400 border border-gray-100">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400 border border-gray-100">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <a href="{{ route('files.view', $file) }}" target="_blank"
                                                                class="text-sm font-extrabold text-gray-700 hover:text-primary transition-colors truncate max-w-[250px]">{{ $file->name }}</a>
                                                            <span
                                                                class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $file->mime_type }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-500">
                                                    {{ number_format($file->size / 1024, 2) }} KB
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-400">
                                                    {{ $file->created_at->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <a href="{{ route('files.download', $file) }}"
                                                            class="p-2 text-gray-400 hover:text-green-600 transition-colors"
                                                            title="Download">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                                </path>
                                                            </svg>
                                                        </a>

                                                        @php
                                                            $driveLink = "https://drive.google.com/file/d/{$file->drive_file_id}/view?usp=sharing";
                                                            $subject = "Berkas: " . $file->name;
                                                            $body = "Berikut adalah link untuk mengunduh berkas yang Anda butuhkan:%0A%0A" . $driveLink . "%0A%0ATerima kasih.";
                                                            $gmailLink = "https://mail.google.com/mail/?view=cm&fs=1&su=" . urlencode($subject) . "&body=" . $body; 
                                                        @endphp
                                                        <a href="{{ $gmailLink }}" target="_blank"
                                                            class="p-2 text-gray-400 hover:text-blue-600 transition-colors"
                                                            title="Gmail Link">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                        </a>

                                                        @if(auth()->user()->isAdmin())
                                                            <form action="{{ route('files.destroy', $file) }}" method="POST"
                                                                class="inline" onsubmit="return confirm('Hapus berkas?');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                    class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium italic">
                                                    Folder ini masih
                                                    kosong.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($items->hasPages())
                                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                                    {{ $items->links() }}
                                </div>
                            @endif
                        </div>


                        <!-- Hidden Form for Bulk Download -->
                        <form id="bulk-download-form" action="{{ route('files.bulk-download') }}" method="POST"
                            style="display: none;">
                            @csrf
                            <div id="bulk-file-ids"></div>
                        </form>

                        <script>
                            // Toggle Checkbox logic
                            const selectAll = document.getElementById('select-all');
                            const checkboxes = document.querySelectorAll('.file-checkbox');
                            const bulkToolbar = document.getElementById('bulk-actions');
                            const selectedCount = document.getElementById('selected-count');

                            if (selectAll) {
                                selectAll.addEventListener('change', function () {
                                    checkboxes.forEach(cb => cb.checked = this.checked);
                                    updateBulkUI();
                                });
                            }

                            function toggleRow(row) {
                                // Optional: Clicking row selects checkbox (if not clicking link)
                            }

                            function updateBulkUI() {
                                const checked = document.querySelectorAll('.file-checkbox:checked');
                                if (checked.length > 0) {
                                    bulkToolbar.style.display = 'flex';
                                    selectedCount.innerText = checked.length;
                                } else {
                                    bulkToolbar.style.display = 'none';
                                }
                            }

                            function downloadSelected() {
                                if (!confirm('Download ' + document.getElementById('selected-count').innerText + ' file sebagai ZIP?')) return;

                                const checked = document.querySelectorAll('.file-checkbox:checked');
                                const form = document.getElementById('bulk-download-form');
                                const container = document.getElementById('bulk-file-ids');

                                // Clear previous inputs
                                container.innerHTML = '';

                                // Add file IDs as hidden inputs
                                checked.forEach(cb => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'file_ids[]';
                                    input.value = cb.value;
                                    container.appendChild(input);
                                });

                                // Submit form
                                form.submit();
                            }

                            async function copyToClipboardAndOpenGmail(items, confirmMessage) {
                                if (items.length === 0) {
                                    alert('Tidak ada file untuk di-email.');
                                    return;
                                }

                                if (!confirm(confirmMessage)) return;

                                // Icons
                                const iconDoc = "https://img.icons8.com/color/48/document--v1.png";
                                const iconImg = "https://img.icons8.com/color/48/image-file.png";

                                let htmlContent = "<ul style='list-style: none; padding-left: 0;'>";
                                let textContent = "";

                                items.forEach(item => {
                                    const iconUrl = item.isImage ? iconImg : iconDoc;
                                    htmlContent += `
                                                                                                                                    <li style="margin-bottom: 8px; display: flex; align-items: center;">
                                                                                                                                        <img src="${iconUrl}" width="24" height="24" style="vertical-align: middle; margin-right: 10px;">
                                                                                                                                        <a href="${item.link}" style="color: #1a0dab; text-decoration: none; font-weight: bold;">${item.name}</a>
                                                                                                                                    </li>`;
                                    textContent += `- ${item.name}: ${item.link}\n`;
                                });
                                htmlContent += "</ul>";

                                try {
                                    const htmlBlob = new Blob([htmlContent], { type: "text/html" });
                                    const textBlob = new Blob([textContent], { type: "text/plain" });

                                    await navigator.clipboard.write([
                                        new ClipboardItem({
                                            "text/html": htmlBlob,
                                            "text/plain": textBlob
                                        })
                                    ]);

                                    // Open Gmail automatically
                                    const subject = encodeURIComponent("Berkas Pilihan");
                                    const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&su=${subject}`;
                                    window.open(gmailUrl, '_blank');

                                } catch (err) {
                                    console.error('Gagal menyalin:', err);
                                    alert('Gagal menyalin otomatis. Membuka Gmail dengan teks biasa.');
                                    const subject = encodeURIComponent("Berkas Pilihan");
                                    const body = encodeURIComponent(textContent);
                                    const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&su=${subject}&body=${body}`;
                                    window.open(gmailUrl, '_blank');
                                }
                            }

                            // Bulk Email (from Checkboxes)
                            function emailLinks() {
                                const checked = document.querySelectorAll('.file-checkbox:checked');
                                if (checked.length === 0) {
                                    alert('Pilih minimal satu file.');
                                    return;
                                }

                                const items = Array.from(checked).map(cb => ({
                                    name: cb.getAttribute('data-file-name'),
                                    link: cb.getAttribute('data-file-link'),
                                    isImage: cb.getAttribute('data-file-is-image') === '1'
                                }));

                                const msg = 'Agar link BISA DIKLIK, sistem akan menyalinnya ke Clipboard.\n\nKlik OK, lalu Paste (Ctrl+V) di Gmail.';
                                copyToClipboardAndOpenGmail(items, msg);
                            }

                            // Folder Email (from API)
                            async function emailFolder(clientId, folderName) {
                                const btn = event.currentTarget;
                                const oldHtml = btn.innerHTML;
                                btn.disabled = true;
                                // Spin icon
                                btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                                try {
                                    let url = `{{ route('files.folder-links') }}?client_id=${clientId}`;
                                    if (folderName) url += `&folder=${encodeURIComponent(folderName)}`;

                                    const resp = await fetch(url);
                                    if (!resp.ok) throw new Error('Network response was not ok');

                                    const data = await resp.json();
                                    if (data.error) throw new Error(data.error);

                                    const items = data.files.map(f => ({
                                        name: f.name,
                                        link: f.link,
                                        isImage: f.is_image
                                    }));

                                    const msg = `Email ${data.count} file dari folder "${data.folder}"?\n\nSistem akan menyalin link ke Clipboard. Klik OK lalu Paste di Gmail.`;
                                    copyToClipboardAndOpenGmail(items, msg);

                                } catch (err) {
                                    console.error(err);
                                    alert('Gagal mengambil data folder.');
                                } finally {
                                    btn.disabled = false;
                                    btn.innerHTML = oldHtml;
                                }
                            }
                        </script>
                    @endif



                    @if(isset($client))
                        <!-- Upload Modal with Datalist -->
                        <div id="uploadModal"
                            class="hidden fixed inset-0 bg-gray-900/40 flex items-center justify-center z-[60] backdrop-blur-sm p-4 animate-in fade-in duration-200">
                            <div
                                class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-100 transition-all">
                                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                                    <div>
                                        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">Unggah Berkas</h3>
                                        <p class="text-[10px] font-bold text-primary uppercase tracking-widest mt-0.5">
                                            {{ $client->name }}</p>
                                    </div>
                                    <button onclick="document.getElementById('uploadModal').classList.add('hidden')"
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12">
                                            </path>
                                        </svg>
                                    </button>
                                </div>

                                <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data"
                                    class="p-8">
                                    @csrf
                                    <input type="hidden" name="client_id" value="{{ $client->id }}">

                                    <div class="space-y-6">
                                        <div>
                                            <label for="description"
                                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Keterangan
                                                Folder / Sub-Folder</label>
                                            <div class="relative">
                                                <input list="descriptions-list" name="description" id="description"
                                                    value="{{ $folderName ?? '' }}"
                                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                                                    placeholder="Ketik atau pilih keterangan..." autocomplete="off">
                                                <datalist id="descriptions-list">
                                                    @foreach($suggestions as $s)
                                                        @if($s) <option value="{{ $s }}"> @endif
                                                    @endforeach
                                                </datalist>
                                                <div
                                                    class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="mt-2 text-[10px] text-gray-400 font-medium italic">File akan dikelompokkan
                                                berdasarkan
                                                keterangan ini.</p>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih
                                                Berkas</label>
                                            <div class="mt-1 flex justify-center px-6 pt-8 pb-10 border-2 border-gray-300 border-dashed rounded-2xl hover:border-primary hover:bg-primary/5 transition-all cursor-pointer relative group"
                                                onclick="document.getElementById('file-input').click()">
                                                <div class="space-y-2 text-center">
                                                    <div
                                                        class="mx-auto w-12 h-12 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                                        <svg class="w-6 h-6" stroke="currentColor" fill="none"
                                                            viewBox="0 0 48 48">
                                                            <path
                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                stroke-width="3" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex text-sm text-gray-600 justify-center">
                                                        <span class="font-extrabold text-primary hover:text-primary-dark">Pilih
                                                            file</span>
                                                        <p class="pl-1 font-medium">atau tarik & lepas</p>
                                                    </div>
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">
                                                        Maksimal 1GB per
                                                        file</p>
                                                </div>
                                                <input id="file-input" name="files[]" type="file" class="sr-only" multiple
                                                    required>
                                            </div>
                                            <!-- Scroll View for File List -->
                                            <div id="file-list"
                                                class="mt-4 space-y-2 max-h-[150px] overflow-y-auto px-1 custom-scrollbar">
                                            </div>
                                        </div>
                                    </div>


                                    <div id="progress-container" class="mt-6 hidden">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-widest">Progress
                                                Unggah</h4>
                                            <span id="overall-status" class="text-xs font-bold text-primary">0/0 Berhasil</span>
                                        </div>
                                        <!-- Scroll View for Progress List -->
                                        <div id="progress-list"
                                            class="space-y-2 max-h-[150px] overflow-y-auto pr-2 custom-scrollbar">
                                            <!-- Progress Items will be added here -->
                                        </div>
                                    </div>

                                    <div class="mt-8 flex justify-end gap-3">
                                        <button type="button"
                                            onclick="document.getElementById('uploadModal').classList.add('hidden')"
                                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Batal</button>
                                        <button type="submit"
                                            class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-dark shadow-lg shadow-primary/20 transition-all"
                                            id="uploadBtn">
                                            Mulai Unggah
                                        </button>
                                    </div>
                                </form>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const fileInput = document.getElementById('file-input');
                                        const fileList = document.getElementById('file-list');
                                        const uploadBtn = document.getElementById('uploadBtn');
                                        const form = document.querySelector('#uploadModal form');
                                        const dropZone = fileInput.closest('.border-2'); // Get the parent div with border-dashed

                                        if (!dropZone) return;

                                        // Prevent default drag behaviors
                                        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                                            dropZone.addEventListener(eventName, preventDefaults, false);
                                            document.body.addEventListener(eventName, preventDefaults, false);
                                        });

                                        function preventDefaults(e) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                        }

                                        // Highlight drop zone
                                        ['dragenter', 'dragover'].forEach(eventName => {
                                            dropZone.addEventListener(eventName, () => dropZone.classList.add('border-primary', 'bg-primary/5'), false);
                                        });

                                        ['dragleave', 'drop'].forEach(eventName => {
                                            dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-primary', 'bg-primary/5'), false);
                                        });

                                        // Handle dropped files
                                        dropZone.addEventListener('drop', e => {
                                            const dt = e.dataTransfer;
                                            const files = dt.files;
                                            handleFiles(files);
                                        });

                                        fileInput.addEventListener('change', function () {
                                            handleFiles(this.files);
                                        });

                                        let queue = [];

                                        function handleFiles(files) {
                                            queue = Array.from(files);
                                            fileList.innerHTML = '';
                                            queue.forEach(file => {
                                                const item = document.createElement('div');
                                                item.className = 'flex items-center justify-between p-2 bg-gray-50 rounded-xl border border-gray-100 animate-in fade-in duration-200';
                                                item.innerHTML = `
                                                                                                                                                <div class="flex items-center gap-2 truncate">
                                                                                                                                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                                                                                                                    </svg>
                                                                                                                                                    <span class="text-xs font-bold text-gray-700 truncate">${file.name}</span>
                                                                                                                                                </div>
                                                                                                                                                <span class="text-[10px] font-bold text-gray-400">${(file.size / 1024).toFixed(1)} KB</span>
                                                                                                                                            `;
                                                fileList.appendChild(item);
                                            });
                                        }

                                        if (form) {
                                            form.addEventListener('submit', async function (e) {
                                                e.preventDefault();
                                                if (queue.length === 0) return;

                                                uploadBtn.disabled = true;
                                                uploadBtn.innerText = 'Mengunggah...';
                                                document.getElementById('progress-container').classList.remove('hidden');

                                                await processQueue();
                                            });
                                        }

                                        const processQueue = async () => {
                                            const progressList = document.getElementById('progress-list');
                                            const overallStatus = document.getElementById('overall-status');
                                            const totalFiles = queue.length;
                                            let completedCount = 0;
                                            let hasErrors = false;

                                            const concurrencyLimit = 3;
                                            let activeUploads = 0;
                                            let currentIndex = 0;

                                            const next = async () => {
                                                if (currentIndex >= totalFiles) return;

                                                const i = currentIndex++;
                                                const file = queue[i];
                                                const progressId = 'prog-' + i;

                                                const item = document.createElement('div');
                                                item.className = 'text-[10px] text-gray-600 bg-gray-50 p-2 rounded-xl border border-gray-100 mb-2 animate-in slide-in-from-bottom-2 duration-300';
                                                item.innerHTML = `
                                                                                                                                                <div class="flex justify-between mb-1.5 px-1">
                                                                                                                                                    <span class="truncate w-2/3 font-bold">${file.name}</span>
                                                                                                                                                    <span id="${progressId}-status" class="font-bold text-primary">0%</span>
                                                                                                                                                </div>
                                                                                                                                                <div class="w-full bg-gray-200 rounded-full h-1 overflow-hidden">
                                                                                                                                                    <div id="${progressId}-bar" class="bg-primary h-1 rounded-full transition-all duration-300" style="width: 0%"></div>
                                                                                                                                                </div>
                                                                                                                                            `;
                                                progressList.appendChild(item);
                                                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                                                activeUploads++;
                                                try {
                                                    await uploadSingleFile(file, i, progressId);
                                                } catch (err) {
                                                    hasErrors = true;
                                                } finally {
                                                    activeUploads--;
                                                    completedCount++;
                                                    overallStatus.textContent = `${completedCount}/${totalFiles} Selesai`;
                                                    await next();
                                                }
                                            };

                                            const initialTasks = [];
                                            for (let i = 0; i < Math.min(concurrencyLimit, totalFiles); i++) {
                                                initialTasks.push(next());
                                            }

                                            await Promise.all(initialTasks);

                                            if (!hasErrors) {
                                                overallStatus.className = 'text-xs font-bold text-green-600';
                                                overallStatus.textContent = 'Semua Berhasil Diunggah!';
                                                setTimeout(() => {
                                                    window.location.reload();
                                                }, 1500);
                                            } else {
                                                uploadBtn.disabled = false;
                                                uploadBtn.innerText = 'Coba Lagi / Selesai';
                                                overallStatus.className = 'text-xs font-bold text-red-600';
                                                overallStatus.textContent = `${completedCount}/${totalFiles} Selesai (Beberapa Gagal)`;
                                                alert('Beberapa berkas gagal diunggah. Silakan periksa status di daftar progress.');
                                            }
                                        };

                                        const uploadSingleFile = (file, index, progressId) => {
                                            return new Promise((resolve, reject) => {
                                                const formData = new FormData();
                                                const clientId = form.querySelector('input[name="client_id"]').value;
                                                const description = form.querySelector('input[name="description"]').value;

                                                formData.append('client_id', clientId);
                                                formData.append('description', description);
                                                formData.append('files[]', file);
                                                formData.append('_token', '{{ csrf_token() }}');

                                                const xhr = new XMLHttpRequest();
                                                const progressBar = document.getElementById(progressId + '-bar');
                                                const statusText = document.getElementById(progressId + '-status');

                                                xhr.upload.addEventListener('progress', function (e) {
                                                    if (e.lengthComputable) {
                                                        const percent = Math.round((e.loaded / e.total) * 100);
                                                        progressBar.style.width = percent + '%';
                                                        statusText.innerText = percent + '%';
                                                    }
                                                });

                                                xhr.addEventListener('load', function () {
                                                    if (xhr.status >= 200 && xhr.status < 300) {
                                                        progressBar.classList.replace('bg-primary', 'bg-green-500');
                                                        statusText.innerText = 'Berhasil';
                                                        statusText.className = 'font-bold text-green-600';
                                                        resolve();
                                                    } else {
                                                        progressBar.classList.replace('bg-primary', 'bg-red-500');
                                                        let errorMsg = 'Gagal';
                                                        try {
                                                            const resp = JSON.parse(xhr.responseText);
                                                            if (resp.error) errorMsg = resp.error;
                                                            else if (resp.message) errorMsg = resp.message;
                                                        } catch (e) { }
                                                        statusText.innerText = errorMsg;
                                                        statusText.className = 'font-bold text-red-600';
                                                        reject(new Error(errorMsg));
                                                    }
                                                });

                                                xhr.addEventListener('error', function () {
                                                    statusText.innerText = 'Network Error';
                                                    statusText.className = 'font-bold text-red-600';
                                                    reject(new Error('Network Error'));
                                                });

                                                xhr.open('POST', '{{ route("files.store") }}');
                                                xhr.setRequestHeader('Accept', 'application/json');
                                                xhr.send(formData);
                                            });
                                        };
                                    });
                                </script>
                            </div>
                        </div>
                    @endif

@endsection