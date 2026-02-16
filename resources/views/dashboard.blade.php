@extends('layouts.app')

@section('content')
    <style>
        /* Custom styles if needed */
    </style>

    <!-- Top Row: Stats & Clock -->
    <div class="flex flex-col lg:flex-row gap-6 mb-8">
        <!-- Clock Widget -->
        <!-- Clock Widget -->
        <div
            class="w-full lg:w-1/3 bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <!-- Decorative Background Blob -->
            <div
                class="absolute -top-10 -right-10 w-32 h-32 bg-blue-50 rounded-full blur-2xl opacity-50 pointer-events-none">
            </div>

            <!-- Header / Clock Section -->
            <div class="z-10 text-center">
                <div class="flex items-center justify-center gap-2 mb-6">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Waktu Sekarang</h3>
                </div>

                <div class="flex items-baseline justify-center gap-2 mb-4">
                    <div id="digital-clock"
                        class="text-6xl font-black text-gray-800 tracking-tighter tabular-nums drop-shadow-sm">--:--</div>
                    <div id="digital-clock-seconds" class="text-2xl font-bold text-gray-300 tracking-tight tabular-nums">--
                    </div>
                </div>
                <div id="date-display-full"
                    class="text-sm font-bold text-blue-600 bg-blue-50/50 py-2 px-4 rounded-full inline-block capitalize">...
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <!-- Stats Grid -->
        <div class="w-full lg:w-2/3 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('clients.index') }}"
                class="block transform transition-transform hover:scale-105 active:scale-95 duration-200">
                <div
                    class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex items-center gap-4 h-full cursor-pointer hover:shadow-md transition-shadow">
                    <div class="p-3 bg-blue-50 rounded-lg text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-black text-[10px] font-bold uppercase tracking-wider">Total Klien</h3>
                        <p class="text-2xl font-bold text-black">{{ $stats['total_clients'] }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('files.index') }}"
                class="block transform transition-transform hover:scale-105 active:scale-95 duration-200">
                <div
                    class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 flex items-center gap-4 h-full cursor-pointer hover:shadow-md transition-shadow">
                    <div class="p-3 bg-green-50 rounded-lg text-green-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-black text-[10px] font-bold uppercase tracking-wider">Total Berkas</h3>
                        <p class="text-2xl font-bold text-black">{{ $stats['total_files'] }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('files.index') }}"
                class="block transform transition-transform hover:scale-105 active:scale-95 duration-200">
                <div
                    class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500 relative overflow-hidden h-full cursor-pointer hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-black text-[10px] font-bold uppercase tracking-wider mb-1">Storage Usage</h3>
                            <p class="text-xl font-bold text-black">{{ $stats['storage']['used_formatted'] }}</p>
                            <p class="text-[10px] text-gray-500">of {{ $stats['storage']['limit_formatted'] }}</p>
                        </div>
                        <div style="height: 60px; width: 60px;">
                            <canvas id="storageChartSmall"></canvas>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column (1/3): Info & Deadlines -->
        <div class="space-y-8">
            <!-- Info Board -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-primary text-white flex justify-between items-center">
                    <h3 class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                            </path>
                        </svg>
                        Papan Informasi
                    </h3>
                    <span
                        class="text-[10px] uppercase font-bold tracking-widest bg-white/20 px-2 py-0.5 rounded">Terbaru</span>
                </div>
                <div class="p-6">
                    @forelse($infos as $info)
                        <div class="group border-b border-black/5 pb-6 mb-6 last:mb-0 last:border-0 last:pb-0">
                            <h4
                                class="text-md font-bold text-black border-l-4 border-primary pl-3 mb-2 group-hover:text-primary-hover transition-colors">
                                {{ $info->title }}
                            </h4>
                            <p class="text-xs text-black font-medium leading-relaxed">{{ Str::limit($info->content, 100) }}</p>
                            <div class="text-[10px] uppercase font-bold text-black/60 mt-4 flex items-center gap-4">
                                <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>{{ $info->creator->name }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-black/40 text-xs italic font-bold">Belum ada informasi terbaru.</p>
                        </div>
                    @endforelse
                </div>
                <!-- Agenda List Footer -->
                <div class="px-6 pb-6 pt-4 border-t border-gray-100 bg-gray-50/30">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Cuti (Leaves) Section -->
                        <div>
                            <h4
                                class="text-[11px] font-bold text-black uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                Cuti Bulan Ini
                            </h4>
                            <div class="space-y-3">
                                @forelse($leaves as $leave)
                                    <div
                                        class="flex items-center gap-3 p-3 rounded-xl bg-white border border-red-50 hover:border-red-200 transition-all shadow-sm">
                                        <div class="relative">
                                            <div
                                                class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border-2 border-white shadow-sm">
                                                @if($leave->user && $leave->user->photo)
                                                    <img src="{{ asset('storage/' . $leave->user->photo) }}"
                                                        alt="{{ $leave->user->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span
                                                        class="text-xs font-bold text-gray-500">{{ substr($leave->title, 0, 2) }}</span>
                                                @endif
                                            </div>
                                            <span
                                                class="absolute bottom-0 right-0 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <h5 class="text-xs font-bold text-black truncate">{{ $leave->title }}</h5>
                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tight">
                                                {{ $leave->start->format('d M') }} -
                                                {{ $leave->end ? $leave->end->format('d M') : 'Sehari' }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-400 text-[10px] italic">Tidak ada yang cuti bulan ini.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Meetings Section -->
                        <div>
                            <h4
                                class="text-[11px] font-bold text-black uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                Meeting & Agenda
                            </h4>
                            <div class="space-y-3">
                                @forelse($upcomingMeetings as $meeting)
                                    <div
                                        class="flex items-center gap-3 p-3 rounded-xl bg-white border border-green-50 hover:border-green-200 transition-all shadow-sm group">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex flex-col items-center justify-center flex-shrink-0 border border-green-100">
                                            <span
                                                class="text-[8px] font-bold uppercase">{{ $meeting->start->format('M') }}</span>
                                            <span class="text-sm font-black">{{ $meeting->start->format('d') }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <h5
                                                class="text-xs font-bold text-black truncate group-hover:text-green-600 transition-colors">
                                                {{ $meeting->title }}
                                            </h5>
                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tight">
                                                {{ $meeting->start->format('H:i') }} •
                                                {{ $meeting->user ? $meeting->user->name : 'System' }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-400 text-[10px] italic">Tidak ada meeting terdekat.</p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Contract Deadlines Alert -->
            @if($contractDeadlines->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
                    <div class="px-6 py-4 bg-red-50 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <h3 class="text-red-800 font-bold text-sm">Deadline Kontrak</h3>
                    </div>
                    <div class="divide-y divide-red-50">
                        @foreach($contractDeadlines as $client)
                            <div class="px-6 py-4 flex justify-between items-center hover:bg-red-50/30 transition-colors">
                                <span class="text-xs font-bold text-black">{{ $client->name }}</span>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-red-600 uppercase">
                                        {{ $client->retainer_contract_end->format('d/m/y') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column (2/3): Large Calendar -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="md:p-8 p-5 bg-white rounded-t">

                    <div class="flex items-center justify-between pt-12 overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-200">

                            <tbody id="calendar-body">
                                <!-- Dynamic Days -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Monthly Agenda List Section -->
                <div class="md:py-8 py-5 md:px-16 px-5 dark:bg-gray-700 bg-gray-50 rounded-b">
                    <div class="flex items-center justify-between mb-6">
                        <button id="prev-month-agenda" class="p-2 hover:bg-gray-200 rounded-full text-gray-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none">
                                <path d="M15 6l-6 6l6 6" />
                            </svg>
                        </button>
                        <h4 id="agenda-month-year"
                            class="text-lg font-bold text-gray-800 uppercase tracking-widest flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div> Agenda
                        </h4>
                        <button id="next-month-agenda" class="p-2 hover:bg-gray-200 rounded-full text-gray-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none">
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                        </button>
                    </div>

                    <div id="agenda-list-content" class="space-y-6">
                        <!-- Dynamic Monthly Agenda List -->
                        <p class="text-center text-gray-400 text-sm italic py-10">Memuat agenda...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
                document.addEventListener('DOMContentLoaded', func              tion              () {
                    // Storage Chart
                    const ctx = document.getElementById('storageChartSmall').getContext('2d');
                    const usageGb = {{ number_format($stats['storage']['usage'] / 1024 / 1024 / 1024, 2, '.', '') }};
                    const limitGb = {{ number_format($stats['storage']['limit'] / 1024 / 1024 / 1024, 2, '.', '') }};
                    const freeGb = Math.max(0, limitGb - usageGb);

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Used', 'Free'],
                            datasets: [{
                                data: [usageGb, freeGb],
                                backgroundColor: ['#ef4444', '#22c55e'],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (context) { return context.label + ': ' + context.raw.toFixed(2) + ' GB'; } } } },
                            cutout: '65%',
                        }
                    });

                    // --- Custom Calendar & Agenda Logic ---
                    const calendarEvents = @json($calendarEvents);
                    let currentMonth = new Date().getMonth();
                    let currentYear = new Date().getFullYear();
                    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

                    // Render Large Calendar Grid (Just the grid, no headers as requested)
                    function renderCalendarGrid(month, year) {
                        const firstDay = (new Date(year, month)).getDay();
                        let startOffset = (firstDay === 0 ? 6 : firstDay - 1);
                        const daysInMonth = 32 - new Date(year, month, 32).getDate();
                        const tbl = document.getElementById("calendar-body");

                        if(tbl) tbl.innerHTML = "";

                        let date = 1;
                        for (let i = 0; i < 6; i++) {
                            const row = document.createElement("tr");
                            if (date > daysInMonth) break;

                            for (let j = 0; j < 7; j++) {
                                const cell = document.createElement("td");
                                cell.className = "w-14 h-14 border border-gray-200 align-top relative hover:bg-gray-50 transition cursor-pointer";

                                if (i === 0 && j < startOffset) {
                                    cell.appendChild(document.createTextNode(""));
                                } else if (date > daysInMonth) {
                                    cell.appendChild(document.createTextNode(""));
                                } else {
                                    const dayVal = date;

                                    // Container
                                    const con = document.createElement("div");
                                    con.className = "w-full h-full flex flex-col items-center justify-start py-1";

                                    // Date Number
                                    const span = document.createElement("span");
                                    span.innerText = date;
                                    span.className = "text-sm font-medium w-6 h-6 flex items-center justify-center rounded-full mb-1";

                                    // Highlight Today
                                    const today = new Date();
                                    if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                                        span.classList.add("bg-[#D4AF37]", "text-white", "font-bold", "shadow-sm");
                                    } else if (j === 5 || j === 6) { 
                                        span.classList.add("text-red-500");
                                        cell.classList.add("bg-red-50/20");
                                    }

                                    // Check events for dots/colors
                                    const checkDate = new Date(year, month, date);
                                    checkDate.setHours(0,0,0,0);
                                     const dayEvents = calendarEvents.filter(e => {
                                        const startData = new Date(e.start);
                                        startData.setHours(0, 0, 0, 0);
                                        const endData = e.end ? new Date(e.end) : new Date(startData);
                                        endData.setHours(0, 0, 0, 0);
                                        return checkDate >= startData && checkDate <= endData;
                                    });

                                     // Holiday Red
                                    const isHoliday = dayEvents.some(e => e.extendedProps && e.extendedProps.isHoliday);
                                    if (isHoliday && !span.classList.contains("bg-[#D4AF37]")) {
                                        span.classList.add("text-red-500", "font-bold");
                                        cell.classList.add("bg-red-50/20");
                                    }

                                    con.appendChild(span);
                                     // Dots
                                    if (dayEvents.length > 0) {
                                        const dotsDiv = document.createElement("div");
                                        dotsDiv.className = "flex gap-0.5 justify-center flex-wrap px-1";
                                        dayEvents.slice(0, 4).forEach(evt => {
                                            const dot = document.createElement("div");
                                            let dotColor = evt.backgroundColor || '#3b82f6';
                                            if (evt.extendedProps && evt.extendedProps.isHoliday) dotColor = '#ef4444';
                                            else if (evt.title.toLowerCase().includes('cuti')) dotColor = '#ef4444';
                                            dot.className = "w-1.5 h-1.5 rounded-full";
                                            dot.style.backgroundColor = dotColor;
                                            dotsDiv.appendChild(dot);
                                        });
                                        con.appendChild(dotsDiv);
                                    }
                                    cell.appendChild(con);
                                    date++;
                                }
                                row.appendChild(cell);
                            }
                            if(tbl) tbl.appendChild(row);
                        }
                    }

                    // Render Monthly Agenda List
                    function renderAgendaList(month, year) {
                        const container = document.getElementById("agenda-list-content");
                        const header = document.getElementById("agenda-month-year");
                        if(!container || !header) return;

                        // Update Header
                        header.innerHTML = `<div class="w-2 h-2 rounded-full bg-blue-500"></div> Agenda ${monthNames[month]} ${year}`;

                        // Filter events for this month
                        const startOfMonth = new Date(year, month, 1);
                        const endOfMonth = new Date(year, month + 1, 0);

                        const monthEvents = calendarEvents.filter(e => {
                            const start = new Date(e.start);
                            // Check if event overlaps with this month
                            const end = e.end ? new Date(e.end) : new Date(start);
                            return start <= endOfMonth && end >= startOfMonth;
                        });

                        if(monthEvents.length === 0) {
                            container.innerHTML = `<p class="text-center text-gray-400 text-sm italic py-10">Tidak ada agenda bulan ini.</p>`;
                            return;
                        }

                        // Group by Date
                        // We iterate through every day of the month to show empty days? 
                        // User input: "Agenda Harian... Selasa, 17 Februari 2026... Kunjungan...". 
                        // Likely only dates with events should be shown to be compact, or maybe all?
                        // Given "Agenda Monthly List", usually only dates with events are shown.
                        // Converting to map: DateString -> [Events]

                        // Better approach: Sort events by start date, then group.
                        monthEvents.sort((a,b) => new Date(a.start) - new Date(b.start));

                        // But we need to handle multi-day events appearing on each day? 
                        // For simplicity and "Agenda Harian" style, let's group by start date for now, 
                        // OR duplicate multi-day events across days?
                        // "Tahun Baru Imlek... dengan agenda selama bulan ini"
                        // Let's iterate days of month and check events for each day.

                        let html = "";
                        const today = new Date();
                        today.setHours(0,0,0,0);

                        const daysInMonth = new Date(year, month + 1, 0).getDate();

                        for(let d=1; d<=daysInMonth; d++) {
                            const currentDt = new Date(year, month, d);
                            // Check events for this day
                             const dayEvents = monthEvents.filter(e => {
                                const s = new Date(e.start); s.setHours(0,0,0,0);
                                const end = e.end ? new Date(e.end) : new Date(s); end.setHours(0,0,0,0);
                                return currentDt >= s && currentDt <= end;
                            });

                            if(dayEvents.length > 0) {
                                const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                                const dateStr = currentDt.toLocaleDateString('id-ID', options);
                                const isPast = currentDt < today;

                                html += `
                                <div class="relative pl-4 border-l-2 ${isPast ? 'border-gray-200' : 'border-blue-200'}">
                                    <h5 class="text-sm font-bold ${isPast ? 'text-gray-400' : 'text-gray-800'} mb-2 flex items-center gap-2">
                                        <span class="absolute -left-[5px] top-1.5 w-2 h-2 rounded-full ${isPast ? 'bg-gray-300' : 'bg-blue-500'}"></span>
                                        ${dateStr}
                                    </h5>
                                    <div class="space-y-2">`;

                                    dayEvents.forEach(e => {
                                        const isHoliday = e.extendedProps && e.extendedProps.isHoliday;
                                        const isCuti = e.title.toLowerCase().includes('cuti') || e.extendedProps.type === 'leave';

                                        let contentClass = "text-gray-800";
                                        if(isPast) {
                                            contentClass = "line-through grayscale opacity-70";
                                            if(isCuti) contentClass += " text-red-400"; // Red but muted if past
                                            else contentClass += " text-gray-400";
                                        } else {
                                            if(isCuti) contentClass = "text-red-600 font-bold";
                                        }

                                        // Specific styling requests
                                        const timeStr = isHoliday ? '' : 
                                            `${new Date(e.start).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}` + 
                                            (e.end ? ` - ${new Date(e.end).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}` : '');

                                        html += `
                                            <div class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm ${contentClass}">
                                                <p class="font-bold text-sm mb-1">${e.title}</p>
                                                ${timeStr ? `<p class="text-xs font-mono opacity-80">${timeStr}</p>` : ''}
                                            </div>
                                        `;
                                    });

                                html += `</div></div>`;
                            }
                        }

                        if(html === "") html = `<p class="text-center text-gray-400 text-sm italic py-10">Tidak ada agenda bulan ini.</p>`;
                        container.innerHTML = html;
                    }

                    // Navigation Handlers
                     const prevBtn = document.getElementById("prev-month-agenda");
                     const nextBtn = document.getElementById("next-month-agenda");

                     if(prevBtn) prevBtn.addEventListener("click", () => {
                         currentMonth--;
                         if(currentMonth < 0) { currentMonth = 11; currentYear--; }
                         renderCalendarGrid(currentMonth, currentYear);
                         renderAgendaList(currentMonth, currentYear);
                     });

                     if(nextBtn) nextBtn.addEventListener("click", () => {
                         currentMonth++;
                         if(currentMonth > 11) { currentMonth = 0; currentYear++; }
                         renderCalendarGrid(currentMonth, currentYear);
                         renderAgendaList(currentMonth, currentYear);
                     });


                    // Initial Render
                    renderCalendarGrid(currentMonth, currentYear);
                    renderAgendaList(currentMonth, currentYear);

                    // --- Helper Logic for Clock ---
                    function updateClock() {
                        const now = new Date();
                        const clockEl = document.getElementById('digital-clock');
                        const secondsEl = document.getElementById('digital-clock-seconds');
                        const dateFullEl = document.getElementById('date-display-full');

                        if (clockEl) {
                            const hours = String(now.getHours()).padStart(2, '0');
                            const minutes = String(now.getMinutes()).padStart(2, '0');
                            clockEl.innerText = `${hours}:${minutes}`;
                        }

                        if (secondsEl) {
                            const seconds = String(now.getSeconds()).padStart(2, '0');
                            secondsEl.innerText = seconds;
                        }

                        if (dateFullEl) {
                            const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                            // Indonesian locale
                            dateFullEl.innerText = now.toLocaleDateString('id-ID', options);
                        }
                    }
                    setInterval(updateClock, 1000);
                    updateClock();
                });
            </script>
@endsection