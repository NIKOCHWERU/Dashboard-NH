{{-- ============================================================ --}}
{{-- UPLOAD MODAL PARTIAL --}}
{{-- Props: $uploadClients, $suggestions, $defaultClient, $defaultFolder --}}
{{-- ============================================================ --}}
<div id="uploadModal"
    class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-[60] p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-primary/10 text-primary rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Upload Berkas</h3>
                    <p class="text-xs text-gray-400">Maks 100 file, maks 1GB per file</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <div id="upload-form-area" class="px-6 py-5 space-y-5">
            {{-- Klien --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Klien <span
                        class="text-red-500">*</span></label>
                <select name="client_id" id="upload-client-id" required
                    class="block w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-gray-50/50">
                    @foreach($uploadClients as $c)
                        <option value="{{ $c->id }}" {{ (isset($defaultClient) && $defaultClient->id == $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Keterangan / Folder --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan / Folder</label>
                <input type="text" name="description" id="upload-description" list="suggestions-list"
                    value="{{ $defaultFolder ?? '' }}" placeholder="Contoh: Kontrak 2025, Invoice, dll."
                    class="block w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-gray-50/50">
                <datalist id="suggestions-list">
                    @foreach($suggestions as $s)
                        <option value="{{ $s }}">
                    @endforeach
                </datalist>
                <p class="text-[11px] text-gray-400 mt-1">Berkas akan dikelompokkan ke dalam folder ini di Google Drive.
                </p>
            </div>

            {{-- File Drop Zone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Berkas <span
                        class="text-red-500">*</span></label>
                <div id="drop-zone"
                    class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center cursor-pointer hover:border-primary/40 hover:bg-primary/5 transition-all group"
                    onclick="document.getElementById('file-input').click()">
                    <div
                        class="mx-auto w-12 h-12 bg-gray-100 group-hover:bg-primary/10 rounded-2xl flex items-center justify-center mb-3 transition-colors">
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-600 group-hover:text-primary transition-colors">Drag &
                        drop atau klik untuk pilih</p>
                    <p class="text-xs text-gray-400 mt-1">Bisa pilih banyak file sekaligus</p>
                    <input type="file" id="file-input" multiple class="hidden">
                </div>
                {{-- Selected files preview --}}
                <div id="file-list" class="mt-3 space-y-2 max-h-40 overflow-y-auto hidden"></div>
            </div>

            {{-- Upload Progress Area --}}
            <div id="progress-area" class="hidden space-y-2 max-h-56 overflow-y-auto"></div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')"
                    class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button type="button" id="start-upload-btn" onclick="startUpload()" disabled
                    class="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const fileList = document.getElementById('file-list');
        const progressArea = document.getElementById('progress-area');
        const uploadBtn = document.getElementById('start-upload-btn');

        let selectedFiles = [];

        // Drag & Drop
        if (dropZone) {
            dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-primary', 'bg-primary/5'); });
            dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('border-primary', 'bg-primary/5'); });
            dropZone.addEventListener('drop', e => {
                e.preventDefault();
                dropZone.classList.remove('border-primary', 'bg-primary/5');
                addFiles(Array.from(e.dataTransfer.files));
            });
        }
        if (fileInput) {
            fileInput.addEventListener('change', () => addFiles(Array.from(fileInput.files)));
        }

        function addFiles(files) {
            selectedFiles = [...selectedFiles, ...files];
            renderFileList();
            if (uploadBtn) uploadBtn.disabled = selectedFiles.length === 0;
        }

        function renderFileList() {
            if (!fileList) return;
            fileList.classList.toggle('hidden', selectedFiles.length === 0);
            fileList.innerHTML = selectedFiles.map((f, i) => `
            <div class="flex items-center justify-between bg-gray-50 rounded-xl px-3 py-2 text-sm">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span class="truncate text-gray-700 font-medium">${f.name}</span>
                </div>
                <button type="button" onclick="removeFile(${i})" class="ml-2 text-gray-400 hover:text-red-500 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        `).join('');
        }

        window.removeFile = function (index) {
            selectedFiles.splice(index, 1);
            renderFileList();
            if (uploadBtn) uploadBtn.disabled = selectedFiles.length === 0;
        };

        window.startUpload = async function () {
            if (!selectedFiles.length) return;
            const clientId = document.getElementById('upload-client-id')?.value;
            const description = document.getElementById('upload-description')?.value || '';
            if (!clientId) { alert('Pilih klien terlebih dahulu.'); return; }

            if (uploadBtn) uploadBtn.disabled = true;
            if (fileList) fileList.classList.add('hidden');
            if (progressArea) progressArea.classList.remove('hidden');
            if (progressArea) progressArea.innerHTML = '';

            let successCount = 0, failCount = 0;

            const uploadPromises = selectedFiles.map((file, index) => {
                const progressId = 'prog-' + index;
                if (progressArea) {
                    progressArea.innerHTML += `
                    <div class="bg-gray-50 rounded-xl p-3">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-xs font-medium text-gray-600 truncate max-w-[200px]">${file.name}</span>
                            <span id="${progressId}-status" class="text-xs font-bold text-gray-400 ml-2">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div id="${progressId}-bar" class="bg-primary h-1.5 rounded-full transition-all duration-300" style="width:0%"></div>
                        </div>
                    </div>`;
                }
                return uploadSingleFile(file, index, progressId, clientId, description)
                    .then(() => successCount++)
                    .catch(() => failCount++);
            });

            await Promise.all(uploadPromises);

            setTimeout(() => {
                if (failCount === 0) {
                    window.location.reload();
                } else {
                    if (uploadBtn) uploadBtn.disabled = false;
                    alert(`${successCount} berkas berhasil, ${failCount} gagal. Periksa status di atas.`);
                }
            }, 800);
        };

        function uploadSingleFile(file, index, progressId, clientId, description) {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('client_id', clientId);
                formData.append('description', description);
                formData.append('files[]', file);
                formData.append('_token', '{{ csrf_token() }}');

                const xhr = new XMLHttpRequest();
                const bar = document.getElementById(progressId + '-bar');
                const status = document.getElementById(progressId + '-status');

                xhr.upload.addEventListener('progress', e => {
                    if (e.lengthComputable) {
                        const pct = Math.round((e.loaded / e.total) * 100);
                        if (bar) bar.style.width = pct + '%';
                        if (status) status.textContent = pct + '%';
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (bar) bar.classList.replace('bg-primary', 'bg-green-500');
                        if (status) { status.textContent = '✓'; status.className = 'text-xs font-bold text-green-600 ml-2'; }
                        resolve();
                    } else {
                        if (bar) bar.classList.replace('bg-primary', 'bg-red-500');
                        let msg = 'Gagal';
                        try {
                            const r = JSON.parse(xhr.responseText);
                            msg = r.error || r.message || msg;
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        if (status) { status.textContent = msg; status.className = 'text-xs font-bold text-red-600 ml-2'; }
                        reject(new Error(msg));
                    }
                });

                xhr.addEventListener('error', () => {
                    if (status) { status.textContent = 'Network Error'; status.className = 'text-xs font-bold text-red-600 ml-2'; }
                    reject(new Error('Network Error'));
                });

                xhr.open('POST', '{{ route("files.store") }}');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.send(formData);
            });
        }
    })();
</script>