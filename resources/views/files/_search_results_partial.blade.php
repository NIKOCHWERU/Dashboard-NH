@if($items->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="mx-auto w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <h3 class="text-gray-700 font-bold text-lg">Tidak ada hasil</h3>
        <p class="text-gray-400 text-sm mt-1">Coba kata kunci lain atau periksa ejaan Anda.</p>
    </div>
@else
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-500">Ditemukan {{ $items->total() }} hasil untuk "{{ $searchTerm }}"</h3>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">File
                        </th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            Klien / Folder</th>
                        <th class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Info
                        </th>
                        <th class="px-5 py-3.5 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            Aksi</th>
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
                                    <span
                                        class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-600 w-fit">{{ $file->client?->name ?? 'N/A' }}</span>
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
                                        class="p-2 text-primary hover:bg-primary/5 rounded-lg transition-colors"
                                        title="Buka Folder">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('files.download', $file) }}"
                                        class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                        title="Download">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
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