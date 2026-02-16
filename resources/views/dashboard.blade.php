@extends('layouts.app')

@section('content')
    <style>
        /* Custom styles if needed */
    </style>

    <!-- Top Row: Stats & Clock -->
    <div class="flex flex-col lg:flex-row gap-6 mb-8">
        <!-- Clock Widget -->
        <div class="w-full lg:w-1/3 bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <h3 class="text-black text-[10px] font-bold uppercase tracking-widest">Waktu Sekarang</h3>
            </div>
            <div id="digital-clock" class="text-5xl font-mono font-bold text-black tracking-tight mb-1">--:--:--</div>
            <div id="date-display" class="text-sm text-black font-bold">...</div>
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
                            <h4 class="text-[11px] font-bold text-black uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                Cuti Bulan Ini
                            </h4>
                            <div class="space-y-3">
                                @forelse($leaves as $leave)
                                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-red-50 hover:border-red-200 transition-all shadow-sm">
                                        <div class="relative">
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border-2 border-white shadow-sm">
                                                @if($leave->user && $leave->user->photo)
                                                    <img src="{{ asset('storage/' . $leave->user->photo) }}" alt="{{ $leave->user->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-xs font-bold text-gray-500">{{ substr($leave->title, 0, 2) }}</span>
                                                @endif
                                            </div>
                                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <h5 class="text-xs font-bold text-black truncate">{{ $leave->title }}</h5>
                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tight">
                                                {{ $leave->start->format('d M') }} - {{ $leave->end ? $leave->end->format('d M') : 'Sehari' }}
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
                            <h4 class="text-[11px] font-bold text-black uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                Meeting & Agenda
                            </h4>
                            <div class="space-y-3">
                                @forelse($upcomingMeetings as $meeting)
                                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-green-50 hover:border-green-200 transition-all shadow-sm group">
                                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex flex-col items-center justify-center flex-shrink-0 border border-green-100">
                                            <span class="text-[8px] font-bold uppercase">{{ $meeting->start->format('M') }}</span>
                                            <span class="text-sm font-black">{{ $meeting->start->format('d') }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <h5 class="text-xs font-bold text-black truncate group-hover:text-green-600 transition-colors">{{ $meeting->title }}</h5>
                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tight">
                                                {{ $meeting->start->format('H:i') }} • {{ $meeting->user ? $meeting->user->name : 'System' }}
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
                    <div class="md:p-8 p-5 dark:bg-gray-800 bg-white rounded-t">
                        <div class="px-4 flex items-center justify-between">
                            <span id="month-year" tabindex="0" class="focus:outline-none text-xl font-bold dark:text-gray-100 text-gray-800">
                                <!-- Dynamic Month Year -->
                            </span>
                            <div class="flex items-center">
                                <button id="prev-month" aria-label="calendar backward" class="focus:text-gray-400 hover:text-gray-400 text-gray-800 dark:text-gray-100 p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <polyline points="15 6 9 12 15 18" />
                                    </svg>
                                </button>
                                <button id="next-month" aria-label="calendar forward" class="focus:text-gray-400 hover:text-gray-400 ml-3 text-gray-800 dark:text-gray-100 p-2"> 
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <polyline points="9 6 15 12 9 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-12 overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th><div class="w-full flex justify-center"><p class="text-base font-medium text-center text-gray-800 dark:text-gray-100">Sen</p></div></th>
                                        <th><div class="w-full flex justify-center"><p class="text-base font-medium text-center text-gray-800 dark:text-gray-100">Sel</p></div></th>
                                        <th><div class="w-full flex justify-center"><p class="text-base font-medium text-center text-gray-800 dark:text-gray-100">Rab</p></div></th>
                                        <th><div class="w-full flex justify-center"><p class="text-base font-medium text-center text-gray-800 dark:text-gray-100">Kam</p></div></th>
                                        <th><div class="w-full flex justify-center"><p class="text-base font-medium text-center text-gray-800 dark:text-gray-100">Jum</p></div></th>
                                        <th><div class="w-full flex justify-center"><p class="text-base font-medium text-center text-gray-800 dark:text-gray-100">Sab</p></div></th>
                                        <th><div class="w-full flex justify-center"><p class="text-base font-medium text-center text-gray-800 dark:text-gray-100">Min</p></div></th>
                                    </tr>
                                </thead>
                                <tbody id="calendar-body">
                                    <!-- Dynamic Days -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Agenda / Events Section -->
                    <div class="md:py-8 py-5 md:px-16 px-5 dark:bg-gray-700 bg-gray-50 rounded-b">
                        <div id="selected-date-events" class="px-4">
                            <!-- Dynamic Event List -->
                            <p class="text-center text-gray-400 text-sm">Pilih tanggal untuk melihat agenda.</p>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <!-- Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
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

                    // --- Custom Calendar Logic ---
                    const calendarEvents = @json($calendarEvents);
                    let currentMonth = new Date().getMonth();
                    let currentYear = new Date().getFullYear();
                    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

                    function renderCalendar(month, year) {
                        const firstDay = new Date(year, month).getDay(); // 0 = Sunday
                        const daysInMonth = 32 - new Date(year, month, 32).getDate();
                        const tbl = document.getElementById("calendar-body");
                        const monthYearLabel = document.getElementById("month-year");

                        // Adjust for Monday start if needed, but UI shows Mo first? 
                        // The provided UI has Mo, Tu, We... so 0 Should be mapped to Monday? 
                        // Standard JS getDay(): 0=Sun, 1=Mon. 
                        // HTML Headers: Mo, Tu, We, Th, Fr, Sa, Su.
                        // So if 1st is Sun (0), it should be in 7th cell?

                        // Let's assume standard grid where Monday is first column.
                        // JS Day: 0(Sun), 1(Mon), 2(Tue)... 6(Sat)
                        // Grid Cols: 0(Mon), 1(Tue)... 5(Sat), 6(Sun)

                        // Calculate starting empty slots
                        let startCol = (firstDay === 0) ? 6 : firstDay - 1;

                        tbl.innerHTML = "";
                        monthYearLabel.innerText = `${monthNames[month]} ${year}`;

                        let date = 1;
                        for (let i = 0; i < 6; i++) {
                            let row = document.createElement("tr");
                            for (let j = 0; j < 7; j++) {
                                let cell = document.createElement("td");
                                cell.className = "pt-6";

                                let div = document.createElement("div");
                                div.className = "px-2 py-2 cursor-pointer flex w-full justify-center";

                                if (i === 0 && j < startCol) {
                                    // Empty previous month days
                                } else if (date > daysInMonth) {
                                    // Empty next month days
                                } else {
                                    // Date Cell

                                    // Check for events
                                    // Convert date to YYYY-MM-DD local
                                    const dateObj = new Date(year, month, date);
                                    // Adjust for timezone offset to compare correctly with server strings usually? 
                                    // Or just string manipulate:
                                    const yearStr = year;
                                    const monthStr = String(month + 1).padStart(2, '0');
                                    const dayStr = String(date).padStart(2, '0');
                                    const dateString = `${yearStr}-${monthStr}-${dayStr}`;

                                    const dayEvents = calendarEvents.filter(e => e.start.startsWith(dateString));
                                    const isToday = (date === new Date().getDate() && year === new Date().getFullYear() && month === new Date().getMonth());

                                    let p = document.createElement("p");
                                    p.className = `text-base font-medium`;
                                    p.innerText = date;

                                    if (isToday) {
                                        // Today Style (Gold Circle)
                                        let activeDiv = document.createElement("div");
                                        activeDiv.className = "w-full h-full";
                                        activeDiv.innerHTML = `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-[#D4AF37] text-white shadow-md">
                                                <span class="text-base font-medium">${date}</span>
                                            </div>`;
                                        div = document.createElement("div"); // Reset div
                                        div.className = "w-full h-full flex items-center justify-center cursor-pointer";
                                        div.appendChild(activeDiv);

                                        // Click to show events
                                        div.onclick = () => showEventDetails(dateString, dayEvents);
                                    } else {
                                        if (dayEvents.length > 0) {
                                            // Has events
                                            p.classList.add("text-gray-800", "font-bold");
                                            // Add dot
                                            let dot = document.createElement("span");
                                            dot.className = "h-1 w-1 bg-[#D4AF37] rounded-full absolute bottom-1";
                                            div.classList.add("relative", "flex-col", "items-center");
                                            div.appendChild(p);
                                            div.appendChild(dot);
                                            // Click handler
                                            div.onclick = () => showEventDetails(dateString, dayEvents);
                                        } else {
                                            p.classList.add("text-gray-500", "hover:text-[#D4AF37]");
                                            div.appendChild(p);
                                        }
                                    }
                                    date++;
                                }

                                cell.appendChild(div);
                                row.appendChild(cell);
                            }
                            tbl.appendChild(row);
                            if (date > daysInMonth) break;
                        }
                    }

                    function showEventDetails(dateStr, events) {
                        const container = document.getElementById('selected-date-events');
                        container.innerHTML = ''; // Clear

                        // Formatter
                        const d = new Date(dateStr);
                        const readableDate = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' });

                        // Header
                        let header = document.createElement('div');
                        header.className = "border-b pb-4 border-gray-400 border-dashed";
                        header.innerHTML = `<p class="text-xs font-light leading-3 text-gray-500 uppercase tracking-widest">${readableDate}</p>`;
                        container.appendChild(header);

                        if (events.length === 0) {
                            let empty = document.createElement('p');
                            empty.className = "text-sm pt-2 text-gray-400 italic";
                            empty.innerText = "Tidak ada agenda.";
                            container.appendChild(empty);
                        } else {
                            events.forEach(ev => {
                                let item = document.createElement('div');
                                item.className = "border-b pb-4 border-gray-400 border-dashed pt-5 last:border-0";

                                // Parse time
                                const time = ev.start.split('T')[1]?.slice(0, 5) || 'All Day';

                                item.innerHTML = `
                                        <p class="text-xs font-bold leading-3 text-[#D4AF37]">${time}</p>
                                        <a class="block text-md font-bold leading-5 text-gray-800 mt-1">${ev.title}</a>
                                        <p class="text-xs pt-1 text-gray-500 capitalize">${ev.extendedProps?.description || ev.type || ''}</p>
                                     `;
                                container.appendChild(item);
                            });
                        }
                    }

                    document.getElementById('prev-month').addEventListener('click', () => {
                        currentMonth--;
                        if (currentMonth < 0) {
                            currentMonth = 11;
                            currentYear--;
                        }
                        renderCalendar(currentMonth, currentYear);
                    });

                    document.getElementById('next-month').addEventListener('click', () => {
                        currentMonth++;
                        if (currentMonth > 11) {
                            currentMonth = 0;
                            currentYear++;
                        }
                        renderCalendar(currentMonth, currentYear);
                    });

                    // Initial Render
                    renderCalendar(currentMonth, currentYear);

                    // Show today's events initially
                    const today = new Date();
                    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
                    const todayEvents = calendarEvents.filter(e => e.start.startsWith(todayStr));
                    showEventDetails(todayStr, todayEvents);

                    // Clock
                    function updateClock() {
                        const now = new Date();
                        const clockEl = document.getElementById('digital-clock');
                        if (clockEl) clockEl.innerText = now.toLocaleTimeString('id-ID', { hour12: false });
                        const dateEl = document.getElementById('date-display');
                        if (dateEl) dateEl.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                    }
                    setInterval(updateClock, 1000);
                    updateClock();
                });
            </script>
@endsection