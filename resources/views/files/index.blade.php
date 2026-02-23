@extends('layouts.app')

@section('content')
    @php
        if (!function_exists('parseSize')) {
            function parseSize($size)
            {
                $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
                $size = preg_replace('/[^0-9\.]/', '', $size);
                if ($unit)
                    return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
                return round($size);
            }
        }
        if (!function_exists('formatBytes')) {
            function formatBytes($bytes, $precision = 1)
            {
                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                $bytes = max($bytes, 0);
                $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                $pow = min($pow, count($units) - 1);
                return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
            }
        }
        if (!function_exists('fileIcon')) {
            function fileIcon($mime, $name = '')
            {
                if (!$mime)
                    return '📄';
                if (str_starts_with($mime, 'image/'))
                    return '🖼️';
                if (str_starts_with($mime, 'video/'))
                    return '🎬';
                if (str_starts_with($mime, 'audio/'))
                    return '🎵';
                if ($mime === 'application/pdf')
                    return '📕';
                if (str_contains($mime, 'word') || str_contains($mime, 'document'))
                    return '📝';
                if (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet'))
                    return '📊';
                if (str_contains($mime, 'presentation') || str_contains($mime, 'powerpoint'))
                    return '📊';
                if (str_contains($mime, 'zip') || str_contains($mime, 'rar') || str_contains($mime, 'archive'))
                    return '🗜️';
                return '📄';
            }
        }
        if (!function_exists('fileColor')) {
            function fileColor($mime)
            {
                if (!$mime)
                    return 'bg-gray-50 text-gray-500';
                if (str_starts_with($mime, 'image/'))
                    return 'bg-purple-50 text-purple-600';
                if (str_starts_with($mime, 'video/'))
                    return 'bg-pink-50 text-pink-600';
                if (str_starts_with($mime, 'audio/'))
                    return 'bg-indigo-50 text-indigo-600';
                if ($mime === 'application/pdf')
                    return 'bg-red-50 text-red-600';
                if (str_contains($mime, 'word') || str_contains($mime, 'document'))
                    return 'bg-blue-50 text-blue-600';
                if (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet'))
                    return 'bg-green-50 text-green-600';
                if (str_contains($mime, 'presentation') || str_contains($mime, 'powerpoint'))
                    return 'bg-orange-50 text-orange-600';
                if (str_contains($mime, 'zip') || str_contains($mime, 'archive'))
                    return 'bg-yellow-50 text-yellow-600';
                return 'bg-gray-50 text-gray-500';
            }
        }
    @endphp

    {{-- ===== HEADER BAR ===== --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex-1 min-w-0">
            {{-- Breadcrumbs --}}
            @if(!empty($breadcrumbs))
                <nav class="flex items-center gap-1.5 text-sm text-gray-400 mb-2 flex-wrap">
                    @foreach($breadcrumbs as $i => $crumb)
                        @if($i < count($breadcrumbs) - 1)
                            <a href="{{ $crumb['url'] }}"
                                class="hover:text-primary transition-colors font-medium">{{ $crumb['label'] }}</a>
                            <svg class="w-4 h-4 opacity-40 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        @else
                            <span class="text-gray-700 font-bold truncate max-w-[200px]">{{ $crumb['label'] }}</span>
                        @endif
                    @endforeach
                </nav>
            @endif

            @if($viewMode == 'categories')
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Manajemen Berkas</h1>
                <p class="text-sm text-gray-400 mt-0.5">Kelola semua dokumen dan berkas klien</p>
            @elseif($viewMode == 'clients')
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ ucfirst($category) }}</h1>
                <p class="text-sm text-gray-400 mt-0.5">Pilih klien untuk melihat berkas</p>
            @elseif($viewMode == 'folders')
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $client->name }}</h1>
                <p class="text-sm text-gray-400 mt-0.5">{{ $client->category }}</p>
            @elseif($viewMode == 'files')
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $folderName ?: 'Tanpa Keterangan' }}</h1>
                <p class="text-sm text-gray-400 mt-0.5">{{ $client->name }} &mdash; {{ $items->total() }} berkas</p>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            @if($viewMode == 'folders' || $viewMode == 'files')
                <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl shadow-lg shadow-primary/20 text-sm transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Berkas
                </button>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2"
            id="flash-success">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
            <button onclick="this.closest('#flash-success').remove()"
                class="ml-auto text-green-400 hover:text-green-600">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div
            class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif


    {{-- ============================================================ --}}
    {{-- VIEW MODE 0: CATEGORIES --}}
    {{-- ============================================================ --}}
    @if($viewMode == 'categories')

        {{-- Stats Strip --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="p-3 bg-primary/10 text-primary rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg></div>
                <div>
                    <div class="text-2xl font-extrabold text-gray-900">{{ number_format($totalFiles) }}</div>
                    <div class="text-xs text-gray-400 font-medium">Total Berkas</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                    </svg></div>
                <div>
                    <div class="text-2xl font-extrabold text-gray-900">{{ formatBytes($totalSize) }} / 200 GB</div>
                    <div class="text-xs text-gray-400 font-medium">Total Penyimpanan Terpakai</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg></div>
                <div>
                    <div class="text-2xl font-extrabold text-gray-900">{{ $totalClients }}</div>
                    <div class="text-xs text-gray-400 font-medium">Total Klien</div>
                </div>
            </div>
        </div>

        {{-- Global Search Bar (Only on Front Page) --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('files.index') }}" class="max-w-3xl mx-auto">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                        placeholder="Cari file client atau dokumen apa saja..."
                        autocomplete="off"
                        class="block w-full pl-12 pr-24 py-4 rounded-2xl border-2 border-gray-100 text-base font-medium focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all bg-gray-50/50">
                    <div id="search-loading" class="hidden absolute inset-y-0 right-28 flex items-center">
                        <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div class="absolute inset-y-2 right-2 flex items-center">
                        <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-primary/20">
                            Cari
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-4 mt-3 px-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pencarian Cepat:</span>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="document.querySelector('input[name=search]').value='Contract'; this.form.submit()" class="text-[10px] font-bold text-primary hover:underline">Contract</button>
                        <button type="button" onclick="document.querySelector('input[name=search]').value='Invoice'; this.form.submit()" class="text-[10px] font-bold text-primary hover:underline">Invoice</button>
                        <button type="button" onclick="document.querySelector('input[name=search]').value='.pdf'; this.form.submit()" class="text-[10px] font-bold text-primary hover:underline">PDF Files</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="files-container" class="transition-all duration-300">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent Files --}}
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <span class="p-1.5 bg-primary/10 text-primary rounded-lg"><svg class="w-4 h-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></span>
                        Berkas Terbaru
                    </h3>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th
                                        class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Berkas</th>
                                    <th
                                        class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Klien</th>
                                    <th
                                        class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Diupload</th>
                                    <th
                                        class="px-5 py-3.5 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentFiles as $file)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <span class="text-lg">{{ fileIcon($file->mime_type) }}</span>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800 truncate max-w-[160px]"
                                                        title="{{ $file->name }}">{{ $file->name }}</p>
                                                    <p class="text-[11px] text-gray-400">{{ formatBytes($file->size) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span
                                                class="inline-flex px-2 py-1 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-600">{{ $file->client->name }}</span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="text-[11px] text-gray-500">
                                                <p class="font-medium">{{ $file->uploader?->name ?? 'N/A' }}</p>
                                                <p class="text-gray-400">{{ $file->created_at->diffForHumans() }}</p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            <a href="{{ route('files.download', $file) }}"
                                                class="inline-flex p-1.5 text-gray-400 hover:text-primary hover:bg-primary/5 rounded-lg transition-colors"
                                                title="Download">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm italic">Belum ada
                                            berkas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Category Cards --}}
            <div>
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2 mb-3">
                    <span class="p-1.5 bg-yellow-50 text-yellow-600 rounded-lg"><svg class="w-4 h-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg></span>
                    Kategori
                </h3>
                <div class="flex flex-col gap-3">
                    @php
                        $catColors = ['Retainer' => 'bg-blue-50 text-blue-600', 'Perorangan' => 'bg-green-50 text-green-600', 'Kantor Narasumber Hukum' => 'bg-amber-50 text-amber-600'];
                    @endphp
                    @foreach($items as $cat)
                        <a href="{{ route('files.index', ['category' => $cat]) }}" class="group">
                            <div
                                class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-primary/20 transition-all flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-3 {{ $catColors[$cat] ?? 'bg-gray-50 text-gray-400' }} rounded-xl group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 group-hover:text-primary transition-colors">
                                            {{ ucfirst($cat) }}
                                        </h4>
                                        <p class="text-[11px] text-gray-400">
                                            {{ $categoryCounts[strtolower($cat)] ?? $categoryCounts[$cat] ?? $categoryCounts[ucfirst($cat)] ?? 0 }}
                                            berkas
                                        </p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-primary group-hover:translate-x-1 transition-all"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

    @endif


    {{-- ============================================================ --}}
    {{-- VIEW MODE 1: CLIENTS --}}
    {{-- ============================================================ --}}
    @if($viewMode == 'clients')
        @if($items->isEmpty())
            <div class="text-center py-20">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-gray-700 font-bold">Tidak ada klien</h3>
                <p class="text-gray-400 text-sm mt-1">Belum ada klien di kategori ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($items as $client)
                    <a href="{{ route('files.index', ['client_id' => $client->id]) }}" class="group block">
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-primary/20 transition-all hover:-translate-y-0.5">
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-primary group-hover:text-white transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span
                                    class="inline-flex px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-lg uppercase">{{ $client->files_count }}
                                    berkas</span>
                            </div>
                            <h4 class="font-extrabold text-gray-900 group-hover:text-primary transition-colors text-base mb-1">
                                {{ $client->name }}
                            </h4>
                            <p class="text-xs text-gray-400">Klik untuk lihat folder &rarr;</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    @endif


    {{-- ============================================================ --}}
    {{-- VIEW MODE 2: FOLDERS --}}
    {{-- ============================================================ --}}
    @if($viewMode == 'folders')

        {{-- Upload Modal (available at folder level) --}}
        @include('files._upload_modal', ['uploadClients' => $uploadClients, 'suggestions' => $suggestions, 'defaultClient' => $client, 'defaultFolder' => ''])

        @if($items->isEmpty())
            <div class="text-center py-20">
                <div class="mx-auto w-20 h-20 bg-yellow-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                    </svg>
                </div>
                <h3 class="text-gray-700 font-bold text-lg">Belum ada folder</h3>
                <p class="text-gray-400 text-sm mt-1">Upload berkas pertama untuk membuat folder keterangan.</p>
                <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                    class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl text-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Berkas
                </button>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($items as $folder)
                    <div class="relative group">
                        <a href="{{ route('files.index', ['client_id' => $client->id, 'folder' => $folder->description]) }}"
                            class="block">
                            <div
                                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center hover:shadow-md hover:border-primary/20 transition-all group-hover:-translate-y-1">
                                <div
                                    class="mx-auto w-14 h-14 mb-3 flex items-center justify-center bg-yellow-50 rounded-2xl text-yellow-500 group-hover:bg-yellow-100 transition-colors">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                    </svg>
                                </div>
                                <h5 class="font-bold text-gray-900 truncate text-sm mb-1" title="{{ $folder->description }}">
                                    {{ $folder->description ?: 'Tanpa Keterangan' }}
                                </h5>
                                <span
                                    class="inline-flex px-2 py-0.5 bg-gray-100 text-[10px] font-bold text-gray-500 rounded-lg group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                    {{ $folder->count }} Berkas
                                </span>
                                @if($folder->last_uploaded_at)
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($folder->last_uploaded_at)->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </a>
                        {{-- Folder Actions --}}
                        <div
                            class="absolute top-2 right-2 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            @if(auth()->user()->isAdmin())
                                <button
                                    onclick="confirmDeleteFolder({{ json_encode($folder->description) }}, {{ $folder->count }}, {{ $client->id }})"
                                    class="bg-white/90 backdrop-blur-sm text-red-500 hover:bg-red-500 hover:text-white p-1.5 rounded-lg shadow-sm border border-red-100 transition-all"
                                    title="Hapus Folder">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                            <button onclick="emailFolder({{ $client->id }}, {{ json_encode($folder->description) }})"
                                class="bg-white/90 backdrop-blur-sm text-blue-500 hover:bg-blue-500 hover:text-white p-1.5 rounded-lg shadow-sm border border-blue-100 transition-all"
                                title="Email Link">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Delete Folder Modal --}}
        <div id="deleteFolderModal"
            class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-[70] p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-2xl bg-red-100 mb-4">
                        <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Hapus Folder?</h3>
                    <p class="text-sm text-gray-500 mb-6">Folder <span id="delFolderName"
                            class="font-bold text-gray-800"></span> berisi <span id="delFileCount"
                            class="font-bold text-gray-800"></span> berkas.<br><span class="text-red-600 font-medium">Semua
                            berkas di Drive akan terhapus permanen.</span></p>
                    <form id="deleteFolderForm" action="{{ route('files.destroyFolder') }}" method="POST">
                        @csrf @method('DELETE')
                        <input type="hidden" name="client_id" id="delClientId">
                        <input type="hidden" name="folder" id="delFolderNameInput">
                        <div class="flex gap-3">
                            <button type="button" onclick="closeDeleteFolderModal()"
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Batal</button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition-all">Ya,
                                Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endif


    {{-- ============================================================ --}}
    {{-- UNIVERSAL SEARCH & FILTERS --}}
    {{-- ============================================================ --}}

    {{-- ============================================================ --}}
    {{-- VIEW MODE 3: FILES LIST --}}
    {{-- ============================================================ --}}
    {{-- ============================================================ --}}
    {{-- VIEW MODE: SEARCH RESULTS --}}
    {{-- ============================================================ --}}
    @if($viewMode == 'search_results')
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">Ditemukan {{ $items->total() }} hasil untuk "{{ $searchTerm }}"</h3>
            <a href="{{ route('files.index') }}" class="text-sm font-bold text-primary hover:underline">&larr; Kembali ke Home</a>
        </div>

        @if($items->isEmpty())
             <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="mx-auto w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-gray-700 font-bold text-lg">Tidak ada hasil</h3>
                <p class="text-gray-400 text-sm mt-1">Coba kata kunci lain atau periksa ejaan Anda.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">File</th>
                                <th class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Klien / Folder</th>
                                <th class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Info</th>
                                <th class="px-5 py-3.5 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($items as $file)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xl">{{ fileIcon($file->mime_type) }}</span>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800">{{ $file->name }}</p>
                                                <p class="text-[11px] text-gray-400">{{ formatBytes($file->size) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-600 w-fit">{{ $file->client?->name ?? 'N/A' }}</span>
                                            <span class="text-[11px] text-gray-400 flex items-center gap-1 leading-none">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                                </svg>
                                                {{ $file->description ?: 'Tanpa Folder' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="text-[11px] text-gray-500 leading-tight">
                                            <p class="font-bold text-gray-700">{{ $file->uploader?->name ?? 'System' }}</p>
                                            <p class="text-gray-400">{{ $file->created_at->format('d M Y') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('files.index', ['client_id' => $file->client_id, 'folder' => $file->description]) }}" 
                                               class="p-2 text-primary hover:bg-primary/5 rounded-lg transition-colors" title="Buka Folder">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('files.download', $file) }}" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Download">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-6">
                {{ $items->appends(['search' => $searchTerm])->links() }}
            </div>
        @endif
    @endif

        {{-- Bulk Actions Bar --}}
        <div id="bulk-actions"
            class="hidden bg-primary/5 border border-primary/20 rounded-2xl p-4 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-primary/10 text-primary rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
                <span class="text-sm font-bold text-gray-700"><span id="selected-count">0</span> berkas dipilih</span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="downloadSelected()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-green-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download ZIP
                </button>
                <button onclick="emailLinks()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-blue-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Email Link
                </button>
            </div>
        </div>

        {{-- Files Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-5 py-4 text-left w-10">
                                <input type="checkbox" id="select-all"
                                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                            </th>
                            <th class="px-5 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama
                                Berkas</th>
                            <th class="px-5 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tipe
                            </th>
                            <th class="px-5 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ukuran
                            </th>
                            <th class="px-5 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Diupload Oleh</th>
                            <th class="px-5 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Tanggal</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($items as $file)
                            <tr class="hover:bg-gray-50/50 transition-colors group" data-file-id="{{ $file->id }}">
                                <td class="px-5 py-4">
                                    <input type="checkbox" name="file_ids[]" value="{{ $file->id }}"
                                        class="file-checkbox rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex-shrink-0 text-xl">{{ fileIcon($file->mime_type) }}</span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate max-w-[220px]"
                                                title="{{ $file->name }}">{{ $file->name }}</p>
                                            <p class="text-[11px] text-gray-400 truncate max-w-[220px]">
                                                {{ $file->description ?: 'Tanpa Keterangan' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @php $ext = pathinfo($file->name, PATHINFO_EXTENSION); @endphp
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-[10px] font-bold uppercase {{ fileColor($file->mime_type) }}">{{ strtoupper($ext) ?: 'FILE' }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-sm text-gray-600 font-medium">{{ formatBytes($file->size) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 bg-primary/10 text-primary rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($file->uploader?->name ?? 'N', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">{{ $file->uploader?->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div>
                                        <p class="text-sm text-gray-600">{{ $file->created_at->format('d M Y') }}</p>
                                        <p class="text-[11px] text-gray-400">{{ $file->created_at->format('H:i') }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('files.view', $file) }}" target="_blank"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Lihat di Drive">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('files.download', $file) }}"
                                            class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Download">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                        <button
                                            onclick="emailSingleFile('{{ $file->drive_file_id }}', '{{ addslashes($file->name) }}')"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Kirim via Email">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        @if(auth()->user()->isAdmin())
                                            <form action="{{ route('files.destroy', $file) }}" method="POST"
                                                onsubmit="return confirm('Hapus berkas ini secara permanen?')" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="mx-auto w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-semibold">Tidak ada berkas ditemukan</p>
                                    <p class="text-gray-400 text-sm mt-1">Coba ubah filter pencarian</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($items->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $items->withQueryString()->links() }}
                </div>
            @endif
        </div>

        {{-- Bulk Download Form --}}
        <form id="bulk-download-form" action="{{ route('files.bulk-download') }}" method="POST" class="hidden">
            @csrf
            <div id="bulk-file-inputs"></div>
        </form>

    @endif


    {{-- ============================================================ --}}
    {{-- SHARED SCRIPTS --}}
    {{-- ============================================================ --}}
    <script>
        // ---- Select All Checkbox ----
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                document.querySelectorAll('.file-checkbox').forEach(cb => cb.checked = this.checked);
                updateBulkBar();
            });
            document.querySelectorAll('.file-checkbox').forEach(cb => {
                cb.addEventListener('change', updateBulkBar);
            });
        }
        function updateBulkBar() {
            const checked = document.querySelectorAll('.file-checkbox:checked');
            const bar = document.getElementById('bulk-actions');
            const count = document.getElementById('selected-count');
            if (bar) {
                bar.style.display = checked.length > 0 ? 'flex' : 'none';
                if (count) count.textContent = checked.length;
            }
        }

        // ---- Bulk Download ----
        function downloadSelected() {
            const checked = document.querySelectorAll('.file-checkbox:checked');
            if (!checked.length) return;
            const form = document.getElementById('bulk-download-form');
            const container = document.getElementById('bulk-file-inputs');
            if (!form || !container) return;
            container.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'file_ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            form.submit();
        }

        // ---- Email Links (Bulk) ----
        function emailLinks() {
            const checked = document.querySelectorAll('.file-checkbox:checked');
            if (!checked.length) { alert('Pilih minimal satu berkas.'); return; }
            const ids = Array.from(checked).map(cb => cb.value);
            const clientId = document.querySelector('[data-client-id]')?.dataset.clientId || null;
            const folder = document.querySelector('[data-folder-name]')?.dataset.folderName || '';
            const url = '{{ route("files.folder-links") }}?client_id=' + clientId + '&folder=' + encodeURIComponent(folder);
            fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(r => r.json())
                .then(data => {
                    if (data.files) {
                        const selectedLinks = data.files.filter(f => ids.some(id => f.link.includes(id)));
                        const subject = encodeURIComponent('Berkas: ' + (folder || 'Tanpa Keterangan'));
                        const body = encodeURIComponent(
                            'Berikut link berkas dari folder "' + (folder || 'Tanpa Keterangan') + '":\n\n' +
                            selectedLinks.map(f => f.name + ':\n' + f.link).join('\n\n') +
                            '\n\nDikirim dari Dashboard NH'
                        );
                        window.open('mailto:?subject=' + subject + '&body=' + body);
                    }
                });
        }

        // ---- Email Single File ----
        function emailSingleFile(driveId, name) {
            const link = 'https://drive.google.com/file/d/' + driveId + '/view?usp=sharing';
            const subject = encodeURIComponent('Berkas: ' + name);
            const body = encodeURIComponent('Berikut link berkas "' + name + '":\n\n' + link + '\n\nDikirim dari Dashboard NH');
            window.open('mailto:?subject=' + subject + '&body=' + body);
        }

        // ---- Email Folder (from folder view) ----
        function emailFolder(clientId, folderName) {
            const baseUrl = '{{ route("files.folder-links") }}';
            fetch(baseUrl + '?client_id=' + clientId + '&folder=' + encodeURIComponent(folderName || ''), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.files && data.files.length > 0) {
                        const subject = encodeURIComponent('Berkas Folder: ' + (data.folder || 'Tanpa Keterangan'));
                        const body = encodeURIComponent(
                            'Berikut link berkas dari folder "' + data.folder + '" (' + data.count + ' berkas):\n\n' +
                            data.files.map(f => '• ' + f.name + ':\n  ' + f.link).join('\n\n') +
                            '\n\nDikirim dari Dashboard NH'
                        );
                        window.open('mailto:?subject=' + subject + '&body=' + body);
                    } else {
                        alert('Tidak ada berkas di folder ini.');
                    }
                });
        }

        // ---- Delete Folder Modal ----
        function confirmDeleteFolder(folderName, count, clientId) {
            document.getElementById('delFolderName').textContent = folderName || 'Tanpa Keterangan';
            document.getElementById('delFileCount').textContent = count;
            document.getElementById('delClientId').value = clientId;
            document.getElementById('delFolderNameInput').value = folderName || '';
            document.getElementById('deleteFolderModal').classList.remove('hidden');
        }
        function closeDeleteFolderModal() {
            document.getElementById('deleteFolderModal').classList.add('hidden');
        }

        // ---- Auto-hide flash after 5s ----
        setTimeout(() => {
            const flash = document.getElementById('flash-success');
            if (flash) flash.style.transition = 'opacity 0.5s', flash.style.opacity = '0', setTimeout(() => flash.remove(), 500);
        }, 5000);

        // ---- Live Search Logic ----
        let searchTimeout = null;
        const searchInput = document.getElementById('search-input');
        const searchLoading = document.getElementById('search-loading');
        const filesContainer = document.getElementById('files-container');
        
        // Save initial content (Categories View) if we are on the front page
        const initialContent = filesContainer ? filesContainer.innerHTML : null;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length === 0) {
                    if (initialContent && filesContainer) {
                        filesContainer.innerHTML = initialContent;
                        filesContainer.style.opacity = '0';
                        setTimeout(() => filesContainer.style.opacity = '1', 50);
                    }
                    searchLoading.classList.add('hidden');
                    return;
                }

                // Show loading spinner
                searchLoading.classList.remove('hidden');

                searchTimeout = setTimeout(() => {
                    const url = new URL('{{ route("files.index") }}');
                    url.searchParams.append('search', query);

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Search failed');
                        return response.text();
                    })
                    .then(html => {
                        if (filesContainer) {
                            filesContainer.style.opacity = '0.5';
                            setTimeout(() => {
                                filesContainer.innerHTML = html;
                                filesContainer.style.opacity = '1';
                            }, 100);
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    })
                    .finally(() => {
                        searchLoading.classList.add('hidden');
                    });
                }, 300); // 300ms debounce
            });
        }
    </script>

@endsection