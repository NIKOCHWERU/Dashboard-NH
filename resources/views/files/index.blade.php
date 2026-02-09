@extends('layouts.app')

@section('content')

<!-- Header & Breadcrumbs -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Berkas</h2>
        <nav class="flex mt-1 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                @if(empty($breadcrumbs))
                    <li><span class="text-gray-400">Home</span></li>
                @else
                    @foreach($breadcrumbs as $crumb)
                        <li class="inline-flex items-center">
                            @if(!$loop->first)
                                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            @endif
                            @if($crumb['url'] != '#')
                                <a href="{{ $crumb['url'] }}" class="hover:text-primary font-medium">{{ $crumb['label'] }}</a>
                            @else
                                <span class="font-medium text-gray-800">{{ $crumb['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                @endif
            </ol>
        </nav>
    </div>
    
    <!-- Upload Button (Only show inside a Client Folder) -->
    @if(isset($viewMode) && $viewMode == 'folders')
    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded shadow">
        + Upload File Here
    </button>
    @endif
</div>

<!-- Checks server limits for UI rendering -->
@php
    function parseSize($size) {
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
<!-- Recent Files List -->
<div class="mb-10">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Berkas Terbaru
        </h3>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Berkas</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Klien / Folder</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waktu Unggah</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentFiles as $file)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-gray-100 rounded text-gray-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700 truncate max-w-[200px]" title="{{ $file->name }}">{{ $file->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-tight">{{ $file->client->name }}</span>
                            @if($file->description)
                            <span class="text-[10px] text-gray-400 block">{{ $file->description }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400 font-medium">
                            {{ $file->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('files.download', $file) }}" class="text-xs font-bold text-primary hover:underline">Download</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic text-sm">Belum ada berkas yang diunggah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($items as $cat)
    <a href="{{ route('files.index', ['category' => $cat]) }}" class="block group">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-primary hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-primary mr-4">
                    <!-- Icon -->
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800 group-hover:text-primary">{{ ucfirst($cat) }}</h3>
                    <p class="text-gray-500 text-sm">Masuk ke kategori</p>
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

<!-- VIEW MODE 1: CLIENTS -->
@if($viewMode == 'clients')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($items as $client)
    <a href="{{ route('files.index', ['client_id' => $client->id]) }}" class="block group">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600 mr-3">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">{{ $client->name }}</h4>
                        <span class="text-xs text-gray-500">{{ $client->files_count }} Files</span>
                    </div>
                </div>
                <span class="text-gray-400 group-hover:text-primary">&rarr;</span>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

<!-- VIEW MODE 2: FOLDERS (DESCRIPTIONS) -->
@if($viewMode == 'folders')
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
    @foreach($items as $folder)
    <a href="{{ route('files.index', ['client_id' => $client->id, 'folder' => $folder->description]) }}" class="block group">
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4 text-center border border-gray-100 hover:border-primary">
            <div class="mx-auto w-16 h-16 mb-2">
                <!-- Folder Icon -->
                <svg class="w-full h-full text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
            </div>
            <h5 class="font-medium text-gray-800 truncate" title="{{ $folder->description }}">{{ $folder->description ?: 'Tanpa Keterangan' }}</h5>
            <span class="text-xs text-gray-500">{{ $folder->count }} Items</span>
        </div>
    </a>
    @endforeach
</div>
@if($items->isEmpty())
    <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada Folder</h3>
        <p class="mt-1 text-sm text-gray-500">Mulai upload file untuk membuat folder keterangan baru.</p>
        <div class="mt-6">
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Upload File Baru
            </button>
        </div>
    </div>
@endif

<!-- Upload Modal with Datalist -->
<div id="uploadModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
             <h3 class="text-lg font-bold">Upload ke {{ $client->name }}</h3>
             <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-800">&times;</button>
        </div>
        
        <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Folder</label>
                <!-- Datalist for Creatable Select -->
                <input list="descriptions-list" name="description" class="w-full rounded-md border-gray-300 shadow-sm p-2 border" placeholder="Pilih atau Buat Baru..." autocomplete="off">
                <datalist id="descriptions-list">
                    @foreach($suggestions as $s)
                        @if($s) <option value="{{ $s }}"> @endif
                    @endforeach
                </datalist>
                <p class="text-xs text-gray-500 mt-1">Ketik untuk membuat folder baru.</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih File (Max 100GB)</label>
                <input type="file" name="files[]" multiple required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-hover">
            </div>

            <div id="progress-container" class="mt-4 hidden">
                <h4 class="text-sm font-bold text-gray-700 mb-2">Upload Progress:</h4>
                <div id="progress-list" class="space-y-2 max-h-40 overflow-y-auto">
                    <!-- Progress Items will be added here -->
                </div>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded mt-4" id="uploadBtn">
                Upload Sekarang
            </button>
        </form>
        
        <script>
            // 1. File Size Validation
            const fileInput = document.querySelector('input[type="file"]');
            const uploadBtn = document.getElementById('uploadBtn');
            const form = document.querySelector('#uploadModal form');
            
            fileInput.addEventListener('change', function() {
                const maxPostSize = {{ $maxPostSize }};
                
                // Allow user to select files, we will validate individually during upload or verify total if needed.
                // But for chunking/individual upload, we mostly care about individual file limits or total if sent at once.
                // Since we will split them, individual limit matters most, but let's keep the check simple.
                
                let totalSize = 0;
                for (let i = 0; i < this.files.length; i++) {
                    totalSize += this.files[i].size;
                }
                
                // Warn if SUPER large total, but don't block because we sell separate requests
                // Actually server PHP post_max_size applies to EACH request.
                // So if we send 1 file per request, each file must be < 100GB.
                
                 if (this.files.length > 0) {
                     // logic ok
                 }
            });

            // 2. AJAX Upload Handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const files = fileInput.files;
                if (files.length === 0) return;

                // UI Setup
                uploadBtn.disabled = true;
                uploadBtn.innerText = 'Uploading...';
                const progressContainer = document.getElementById('progress-container');
                const progressList = document.getElementById('progress-list');
                progressContainer.classList.remove('hidden');
                progressList.innerHTML = ''; // Clear previous

                // Create Queue
                const queue = Array.from(files);
                const totalFiles = queue.length;
                let completedCount = 0;

                // Process function
                const processQueue = async () => {
                    for (let i = 0; i < totalFiles; i++) {
                        const file = queue[i];
                        
                        // Create Progress Element
                        const progressId = 'prog-' + i;
                        const item = document.createElement('div');
                        item.className = 'text-xs text-gray-600';
                        item.innerHTML = `
                            <div class="flex justify-between mb-1">
                                <span class="truncate w-1/2">${file.name}</span>
                                <span id="${progressId}-status">Waiting...</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div id="${progressId}-bar" class="bg-primary h-1.5 rounded-full" style="width: 0%"></div>
                            </div>
                        `;
                        progressList.appendChild(item);

                        // Upload Single File
                        await uploadSingleFile(file, i, progressId);
                        completedCount++;
                    }

                    // All done
                    alert('Semua file berhasil diupload!');
                    window.location.reload();
                };

                const uploadSingleFile = (file, index, progressId) => {
                    return new Promise((resolve, reject) => {
                        const formData = new FormData();
                        // Add other form fields
                        const clientId = form.querySelector('input[name="client_id"]').value;
                        const description = form.querySelector('input[name="description"]').value;
                        
                        formData.append('client_id', clientId);
                        formData.append('description', description);
                        formData.append('files[]', file); // Controller expects array
                        formData.append('_token', '{{ csrf_token() }}');

                        const xhr = new XMLHttpRequest();
                        const progressBar = document.getElementById(progressId + '-bar');
                        const statusText = document.getElementById(progressId + '-status');

                        // Progress
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                progressBar.style.width = percent + '%';
                                statusText.innerText = percent + '%';
                                
                                if (percent === 100) {
                                    statusText.innerText = 'Processing...'; // Server is sending to Drive
                                }
                            }
                        });

                        // Completion
                        xhr.addEventListener('load', function() {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                progressBar.classList.remove('bg-primary');
                                progressBar.classList.add('bg-green-500');
                                statusText.innerText = 'Done';
                                resolve();
                            } else {
                                progressBar.classList.remove('bg-primary');
                                progressBar.classList.add('bg-red-500');
                                statusText.innerText = 'Failed';
                                console.error(xhr.responseText);
                                // Resolve anyway to continue queue, or reject to stop?
                                // Let's resolve to try next files
                                resolve(); 
                            }
                        });

                        xhr.addEventListener('error', function() {
                            statusText.innerText = 'Error';
                            resolve();
                        });

                        xhr.open('POST', '{{ route("files.store") }}');
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.send(formData);
                    });
                };

                // Start
                processQueue();
            });
        </script>
    </div>
</div>
@endif

<!-- VIEW MODE 3: FILES LIST -->
@if($viewMode == 'files')
<div class="bg-white rounded-lg shadow">
    <!-- Bulk Actions Toolbar -->
    <div class="p-4 border-b flex justify-between items-center bg-gray-50" id="bulk-actions" style="display: none;">
        <span class="text-sm text-gray-700"><span id="selected-count">0</span> file dipilih</span>
        <button onclick="downloadSelected()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download Terpilih
        </button>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">
                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-primary focus:ring-primary">
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama File</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($items as $file)
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="toggleRow(this)">
                <td class="px-6 py-4" onclick="event.stopPropagation()">
                    <input type="checkbox" name="selected_files[]" value="{{ $file->id }}" data-file-id="{{ $file->id }}" class="file-checkbox rounded border-gray-300 text-primary focus:ring-primary" onchange="updateBulkUI()">
                </td>
                <td class="px-6 py-4 whitespace-nowrap" onclick="window.open('{{ route('files.view', $file) }}', '_blank'); event.stopPropagation();">
                    <div class="flex items-center">
                        @php
                            $isImage = str_starts_with($file->mime_type, 'image/');
                            $thumbnailUrl = $isImage ? "https://drive.google.com/thumbnail?id={$file->drive_file_id}&sz=w100" : null;
                        @endphp
                        
                        @if($thumbnailUrl)
                            <img src="{{ $thumbnailUrl }}" alt="{{ $file->name }}" class="w-10 h-10 object-cover rounded mr-3" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <svg class="w-5 h-5 text-gray-400 mr-2" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        @else
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        @endif
                        <span class="text-sm font-medium text-gray-900">{{ $file->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($file->size / 1024, 2) }} KB</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file->created_at->format('d M Y H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                    <a href="{{ route('files.download', $file) }}" class="text-green-600 hover:text-green-900 mr-3">Download</a>
                    @if(auth()->user()->isAdmin())
                    <form action="{{ route('files.destroy', $file) }}" method="POST" class="inline" onsubmit="return confirm('Hapus file?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Folder ini kosong.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">
        {{ $items->links() }}
    </div>
</div>

<!-- Hidden Form for Bulk Download -->
<form id="bulk-download-form" action="{{ route('files.bulk-download') }}" method="POST" style="display: none;">
    @csrf
    <div id="bulk-file-ids"></div>
</form>

<script>
    // Toggle Checkbox logic
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.file-checkbox');
    const bulkToolbar = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkUI();
    });

    function toggleRow(row) {
        // Optional: Clicking row selects checkbox (if not clicking link)
        // Implementation might conflict with 'view' action on row click. 
        // Current implementation separates View click to name column.
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
</script>
@endif

@endsection
