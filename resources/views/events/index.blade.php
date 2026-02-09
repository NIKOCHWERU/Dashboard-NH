@extends('layouts.app')

@section('content')
<style>
    /* Google Calendar-like Styling */
    .fc {
        max-width: 100%;
        background: white;
        border-radius: 8px;
    }
    .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 600 !important;
        color: #374151;
    }
    
    /* Mobile responsive toolbar */
    @media (max-width: 640px) {
        .fc-toolbar-title {
            font-size: 1rem !important;
        }
        .fc .fc-toolbar {
            flex-direction: column;
            gap: 0.5rem;
        }
        .fc .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
        }
    }
    
    .fc-button-primary {
        background-color: transparent !important;
        border: 1px solid #d1d5db !important;
        color: #374151 !important;
        text-transform: capitalize !important;
        box-shadow: none !important;
        font-weight: 500 !important;
        font-size: 0.875rem !important;
        padding: 0.375rem 0.75rem !important;
    }
    .fc-button-primary:hover {
        background-color: #f3f4f6 !important;
    }
    .fc-button-active {
        background-color: #e5e7eb !important;
        border-color: #9ca3af !important;
    }
    .fc-daygrid-day-number {
        font-size: 0.875rem;
        color: #4b5563;
        padding: 4px 8px !important;
    }
    .fc-col-header-cell-cushion {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        padding: 8px 0 !important;
    }
    .fc-daygrid-day-top {
        flex-direction: column !important;
        align-items: center !important;
    }
    .fc-day-today {
        background-color: #f0f7ff !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        background: #2563eb;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 4px;
    }
    
    /* Hide event titles, show only dots */
    .fc-event-title {
        display: none !important;
    }
    
    /* Event dots */
    .event-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
        margin: 2px;
    }
    
    .event-dots-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding: 2px;
        gap: 2px;
    }
    
    /* Holiday styling */
    .holiday-bg {
        background-color: #fee2e2 !important;
    }
    .holiday-text {
        color: #dc2626 !important;
        font-weight: 800 !important;
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    /* Category select with add new */
    #categorySelect:focus {
        outline: none;
        border-color: #D4AF37;
        ring: 2px;
        ring-color: rgba(212, 175, 55, 0.2);
    }
</style>

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Kalender & Agenda</h2>
        <p class="text-sm text-gray-500">Kelola jadwal dan pantau hari libur nasional.</p>
    </div>
    <div class="flex gap-2">
        @if($canManage)
        <button onclick="openEventModal()" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Event
        </button>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
    <!-- Main Calendar (3/4) -->
    <div class="xl:col-span-3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2 md:p-4">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Sidebar Info (1/4) -->
    <div class="space-y-6">
        <!-- Upcoming Events Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-700">Agenda Terdekat</h3>
                <span class="text-xs font-medium text-primary bg-primary/10 px-2 py-0.5 rounded-full">Baru</span>
            </div>
            <div class="p-5">
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($events->sortBy('start')->take(10) as $event)
                    <div class="group relative pl-4 border-l-2" style="border-color: {{ $event->category ? $event->category->color : '#3b82f6' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-primary transition-colors">{{ $event->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $event->start->format('d M, H:i') }}</p>
                                @if($event->category)
                                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] rounded-full" style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                    {{ $event->category->name }}
                                </span>
                                @endif
                            </div>
                            @if($canManage)
                            <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Hapus agenda ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <p class="text-xs text-gray-400">Tidak ada agenda mendatang.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
                <p class="text-[10px] text-gray-400">Menampilkan 10 agenda terbaru</p>
            </div>
        </div>

        <!-- Legend Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-bold text-gray-700 mb-4 text-sm">Keterangan Warna</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-red-600"></span>
                    <span class="text-xs text-gray-600">Libur Nasional / Cuti</span>
                </div>
                @foreach($categories as $category)
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->color }}"></span>
                    <span class="text-xs text-gray-600">{{ $category->name }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="eventModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[60] backdrop-blur-sm transition-opacity duration-300 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Buat Agenda Baru</h3>
            <button onclick="closeEventModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Judul Agenda</label>
                    <input type="text" name="title" required placeholder="Contoh: Sidang di PN Jakarta Pusat" class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kategori</label>
                    <select id="categorySelect" name="category_id" class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                        <option value="new">+ Tambah Kategori Baru</option>
                    </select>
                </div>
                <div id="newCategoryFields" class="hidden space-y-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Nama Kategori Baru</label>
                        <input type="text" id="newCategoryName" name="new_category_name" placeholder="Contoh: Konsultasi Klien" class="block w-full rounded-lg border-gray-200 shadow-sm p-2 border text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Warna</label>
                        <div class="flex gap-2">
                            <input type="color" id="newCategoryColor" name="new_category_color" value="#3b82f6" class="h-10 w-16 rounded border border-gray-200">
                            <input type="text" id="colorHex" value="#3b82f6" class="flex-1 rounded-lg border-gray-200 p-2 border text-sm font-mono" readonly>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Waktu Mulai</label>
                        <input type="datetime-local" id="event-start" name="start" required class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Waktu Selesai</label>
                        <input type="datetime-local" name="end" class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Lampiran Foto (Opsional)</label>
                    <input type="file" name="photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                    <p class="mt-1 text-[10px] text-gray-400 italic">PNG, JPG, GIF max 10MB</p>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="closeEventModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-dark shadow-lg shadow-primary/20 transition-all">Simpan Agenda</button>
            </div>
        </form>
    </div>
</div>

<!-- Date Detail Modal -->
<div id="dateDetailModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[60] backdrop-blur-sm transition-opacity duration-300 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-primary to-primary-dark flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-white" id="dateDetailTitle">Detail Tanggal</h3>
                <p class="text-xs text-white/80 mt-0.5" id="dateDetailSubtitle"></p>
            </div>
            <button onclick="closeDateDetailModal()" class="text-white/80 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6 max-h-[60vh] overflow-y-auto">
            <!-- Holiday Section -->
            <div id="holidaySection" class="hidden mb-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h4 class="font-bold text-red-800" id="holidayName">Hari Libur Nasional</h4>
                    </div>
                </div>
            </div>
            
            <!-- Events Section -->
            <div id="eventsSection">
                <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Agenda Hari Ini
                </h4>
                <div id="eventsList" class="space-y-3">
                    <!-- Events will be inserted here -->
                </div>
                <div id="noEvents" class="hidden text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-sm text-gray-400">Tidak ada agenda pada tanggal ini</p>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
            <button onclick="closeDateDetailModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Tutup</button>
            @if($canManage)
            <button onclick="addEventFromDateDetail()" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-dark shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Agenda
            </button>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div id="eventModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[60] backdrop-blur-sm transition-opacity duration-300 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Buat Agenda Baru</h3>
            <button onclick="closeEventModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Judul Agenda</label>
                    <input type="text" name="title" required placeholder="Contoh: Sidang di PN Jakarta Pusat" class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Kategori</label>
                    <select id="categorySelect" name="category_id" class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                        <option value="new">+ Tambah Kategori Baru</option>
                    </select>
                </div>
                <div id="newCategoryFields" class="hidden space-y-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Nama Kategori Baru</label>
                        <input type="text" id="newCategoryName" name="new_category_name" placeholder="Contoh: Konsultasi Klien" class="block w-full rounded-lg border-gray-200 shadow-sm p-2 border text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Warna</label>
                        <div class="flex gap-2">
                            <input type="color" id="newCategoryColor" name="new_category_color" value="#3b82f6" class="h-10 w-16 rounded border border-gray-200">
                            <input type="text" id="colorHex" value="#3b82f6" class="flex-1 rounded-lg border-gray-200 p-2 border text-sm font-mono" readonly>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Waktu Mulai</label>
                        <input type="datetime-local" id="event-start" name="start" required class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Waktu Selesai</label>
                        <input type="datetime-local" name="end" class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Lampiran Foto (Opsional)</label>
                    <input type="file" name="photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                    <p class="mt-1 text-[10px] text-gray-400 italic">PNG, JPG, GIF max 10MB</p>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="closeEventModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-dark shadow-lg shadow-primary/20 transition-all">Simpan Agenda</button>
            </div>
        </form>
    </div>
</div>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
const canManage = {{ $canManage ? 'true' : 'false' }};
let selectedDate = null;
const allEvents = @json($calendarEvents);

// Helper to get local YYYY-MM-DD
function getLocalDateStr(date) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Category dropdown handler
document.getElementById('categorySelect')?.addEventListener('change', function() {
    const newCategoryFields = document.getElementById('newCategoryFields');
    if (this.value === 'new') {
        newCategoryFields.classList.remove('hidden');
        document.getElementById('newCategoryName').required = true;
    } else {
        newCategoryFields.classList.add('hidden');
        document.getElementById('newCategoryName').required = false;
    }
});

// Color picker sync
document.getElementById('newCategoryColor')?.addEventListener('input', function() {
    document.getElementById('colorHex').value = this.value;
});

function showDateDetail(dateStr) {
    selectedDate = dateStr;
    const date = new Date(dateStr + 'T00:00:00');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = date.toLocaleDateString('id-ID', options);
    
    document.getElementById('dateDetailTitle').textContent = formattedDate;
    document.getElementById('dateDetailSubtitle').textContent = dateStr;
    
    const eventsOnDay = allEvents.filter(e => e.start.startsWith(dateStr));
    const holiday = eventsOnDay.find(e => e.extendedProps && e.extendedProps.isHoliday);
    const regularEvents = eventsOnDay.filter(e => !e.extendedProps || !e.extendedProps.isHoliday);
    
    const holidaySection = document.getElementById('holidaySection');
    if (holiday) {
        document.getElementById('holidayName').textContent = holiday.title;
        holidaySection.classList.remove('hidden');
    } else {
        holidaySection.classList.add('hidden');
    }
    
    const eventsList = document.getElementById('eventsList');
    const noEvents = document.getElementById('noEvents');
    
    if (regularEvents.length > 0) {
        eventsList.innerHTML = '';
        regularEvents.forEach(event => {
            const eventDiv = document.createElement('div');
            eventDiv.className = 'border-l-4 pl-4 py-2 hover:bg-gray-50 rounded-r transition-colors';
            eventDiv.style.borderColor = event.extendedProps.categoryColor || event.backgroundColor;
            
            const startTime = new Date(event.start).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            const endTime = event.end ? new Date(event.end).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';
            
            eventDiv.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h5 class="font-bold text-gray-900 text-sm">${event.title}</h5>
                        <p class="text-xs text-gray-500 mt-1">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            ${startTime}${endTime ? ' - ' + endTime : ''}
                        </p>
                        ${event.extendedProps.categoryName ? `
                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] rounded-full" style="background-color: ${event.extendedProps.categoryColor}20; color: ${event.extendedProps.categoryColor}">
                                ${event.extendedProps.categoryName}
                            </span>
                        ` : ''}
                    </div>
                </div>
            `;
            eventsList.appendChild(eventDiv);
        });
        eventsList.classList.remove('hidden');
        noEvents.classList.add('hidden');
    } else {
        eventsList.classList.add('hidden');
        noEvents.classList.remove('hidden');
    }
    
    document.getElementById('dateDetailModal').classList.remove('hidden');
}

function closeDateDetailModal() {
    document.getElementById('dateDetailModal').classList.add('hidden');
    selectedDate = null;
}

function addEventFromDateDetail() {
    closeDateDetailModal();
    openEventModal(selectedDate);
}

function openEventModal(dateStr = null) {
    const modal = document.getElementById('eventModal');
    modal.classList.remove('hidden');
    if (dateStr) {
        document.getElementById('event-start').value = dateStr + 'T09:00';
    }
}

function closeEventModal() {
    const modal = document.getElementById('eventModal');
    modal.classList.add('hidden');
    document.getElementById('event-start').value = '';
    document.getElementById('categorySelect').value = '';
    document.getElementById('newCategoryFields').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: window.innerWidth < 768 ? 'listMonth' : 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            list: 'List'
        },
        events: allEvents,
        dayMaxEvents: false,
        eventDisplay: 'none', // Hide event bars
        dateClick: function(info) {
            showDateDetail(info.dateStr);
        },
        eventClick: function(info) {
            if (!info.event.extendedProps.isHoliday) {
                const dateStr = getLocalDateStr(info.event.start);
                showDateDetail(dateStr);
            }
        },
        dayCellDidMount: function(info) {
            const dateStr = getLocalDateStr(info.date);
            const eventsOnDay = allEvents.filter(e => e.start.startsWith(dateStr));
            const holiday = eventsOnDay.find(e => e.extendedProps && e.extendedProps.isHoliday);
            const regularEvents = eventsOnDay.filter(e => !e.extendedProps || !e.extendedProps.isHoliday);
            
            // Holiday styling
            if (holiday) {
                info.el.classList.add('holiday-bg');
                const dayNumber = info.el.querySelector('.fc-daygrid-day-number');
                if (dayNumber) {
                    dayNumber.classList.add('holiday-text');
                }
                info.el.title = holiday.title;
            }
            
            // Add color dots for events
            if (regularEvents.length > 0) {
                const dayTop = info.el.querySelector('.fc-daygrid-day-top');
                if (dayTop) {
                    const dotsContainer = document.createElement('div');
                    dotsContainer.className = 'event-dots-container';
                    
                    regularEvents.slice(0, 4).forEach(event => {
                        const dot = document.createElement('div');
                        dot.className = 'event-dot';
                        dot.style.backgroundColor = event.extendedProps.categoryColor || event.backgroundColor;
                        dot.title = event.title;
                        dotsContainer.appendChild(dot);
                    });
                    
                    if (regularEvents.length > 4) {
                        const moreDot = document.createElement('span');
                        moreDot.className = 'text-[8px] text-gray-500 font-bold';
                        moreDot.textContent = '+' + (regularEvents.length - 4);
                        dotsContainer.appendChild(moreDot);
                    }
                    
                    dayTop.appendChild(dotsContainer);
                }
            }
        }
    });
    calendar.render();
    
    // Responsive view change
    window.addEventListener('resize', function() {
        if (window.innerWidth < 768) {
            calendar.changeView('listMonth');
        } else {
            calendar.changeView('dayGridMonth');
        }
    });
});
</script>
@endsection
