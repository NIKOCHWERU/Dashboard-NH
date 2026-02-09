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
    .fc-button-primary {
        background-color: transparent !important;
        border: 1px solid #d1d5db !important;
        color: #374151 !important;
        text-transform: capitalize !important;
        box-shadow: none !important;
        font-weight: 500 !important;
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
    
    /* Event Styles */
    .fc-v-event, .fc-h-event {
        border: none !important;
        padding: 1px 4px !important;
        border-radius: 4px !important;
    }
    .event-holiday {
        background-color: #fee2e2 !important;
        border: none !important;
        cursor: pointer !important;
    }
    .holiday-text {
        color: #dc2626 !important;
        font-weight: 800 !important;
    }
    .event-meeting {
        background-color: #dcfce7 !important;
        color: #15803d !important;
        border-left: 3px solid #22c55e !important;
    }
    .event-deadline {
        background-color: #fef9c3 !important;
        color: #a16207 !important;
        border-left: 3px solid #eab308 !important;
    }
    .event-general {
        background-color: #dbeafe !important;
        color: #1d4ed8 !important;
        border-left: 3px solid #3b82f6 !important;
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
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
                    <div class="group relative pl-4 border-l-2 {{ $event->type == 'meeting' ? 'border-green-500' : ($event->type == 'deadline' ? 'border-yellow-500' : 'border-blue-500') }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-primary transition-colors">{{ $event->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $event->start->format('d M, H:i') }}</p>
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
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-xs text-gray-600">Meeting Klien</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="text-xs text-gray-600">Deadline Penting</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-xs text-gray-600">Agenda Umum</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="eventModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[60] backdrop-blur-sm transition-opacity duration-300">
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
                    <select name="type" required class="mt-1 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 p-2.5 border transition-all text-sm">
                        <option value="general">Umum</option>
                        <option value="meeting">Meeting Klien</option>
                        <option value="deadline">Deadline Penting</option>
                    </select>
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
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari',
            list: 'Agenda'
        },
        events: @json($calendarEvents),
        dayMaxEvents: 3,
        dateClick: function(info) {
            if (canManage) {
                openEventModal(info.dateStr);
            } else {
                alert('Anda tidak memiliki izin untuk membuat agenda.');
            }
        },
        eventClick: function(info) {
            if (!info.event.extendedProps.isHoliday) {
                alert('Agenda: ' + info.event.title + '\nTipe: ' + (info.event.extendedProps.type || 'Umum'));
            }
        },
        eventClassNames: function(arg) {
            let classes = ['text-xs', 'py-1', 'px-2', 'mb-1'];
            if (arg.event.extendedProps.isHoliday) {
                classes.push('event-holiday');
            } else {
                classes.push('event-' + (arg.event.extendedProps.type || 'general'));
            }
            return classes;
        },
        eventDidMount: function(info) {
            if (info.event.extendedProps.isHoliday) {
                // Set tooltip
                info.el.title = info.event.title + ' (Libur Nasional)';
                
                // Color the day number red if possible
                const cell = info.el.closest('.fc-daygrid-day');
                if (cell) {
                    const dayNumber = cell.querySelector('.fc-daygrid-day-number');
                    if (dayNumber) {
                        dayNumber.classList.add('holiday-text');
                    }
                }
                
                // If it's a background event, we might want to hide the title if FullCalendar shows it
                if (info.event.display === 'background') {
                    const titleEl = info.el.querySelector('.fc-event-title');
                    if (titleEl) titleEl.style.display = 'none';
                }
            } else {
                info.el.title = info.event.title + ' (' + (info.event.extendedProps.type || 'umum') + ')';
            }
        }
    });
    calendar.render();
});
</script>
@endsection
