@extends('layouts.app')

@section('content')
    <style>
        /* Mini Calendar Styles */
        .fc-mini {
            font-size: 1rem;
            color: #000 !important;
            font-weight: 700;
        }

        .fc-mini .fc-toolbar-title {
            font-size: 1.2rem !important;
            font-weight: 800;
            color: #000;
        }

        .fc-mini .fc-button {
            padding: 0.4rem 0.6rem !important;
            font-size: 0.9rem !important;
            background: #D4AF37 !important;
            border: none !important;
        }

        .fc-mini .fc-daygrid-day-number {
            padding: 6px !important;
            color: #000 !important;
            font-weight: 800;
        }

        .fc-mini .fc-col-header-cell-cushion {
            padding: 8px 0 !important;
            color: #000 !important;
            font-weight: 800;
            text-transform: uppercase;
        }

        .fc-mini .fc-day-today {
            background-color: #fff9e6 !important;
        }

        .fc-mini .fc-day-today .fc-daygrid-day-number {
            background: #D4AF37;
            color: white !important;
            border-radius: 6px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .holiday-bg {
            background-color: #fee2e2 !important;
        }

        .holiday-bg .fc-daygrid-day-number {
            color: #dc2626 !important;
        }
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
            <a href="{{ route('clients.index') }}" class="block transform transition-transform hover:scale-105 active:scale-95 duration-200">
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex items-center gap-4 h-full cursor-pointer hover:shadow-md transition-shadow">
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

            <a href="{{ route('files.index') }}" class="block transform transition-transform hover:scale-105 active:scale-95 duration-200">
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 flex items-center gap-4 h-full cursor-pointer hover:shadow-md transition-shadow">
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

            <a href="{{ route('files.index') }}" class="block transform transition-transform hover:scale-105 active:scale-95 duration-200">
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500 relative overflow-hidden h-full cursor-pointer hover:shadow-md transition-shadow">
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
                                {{ $info->title }}</h4>
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
                                        {{ $client->retainer_contract_end->format('d/m/y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column (2/3): Large Calendar -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-black text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Kalender Utama
                    </h3>
                    <a href="{{ route('events.index') }}"
                        class="text-[10px] font-bold text-primary uppercase hover:underline">Full View</a>
                </div>
                <div class="p-4 fc-mini">
                    <div id="mini-calendar" style="min-height: 450px;"></div>
                </div>

                <!-- Agenda List Footer -->
                <div class="px-6 pb-6 pt-4 border-t border-gray-100 bg-gray-50/30">
                    <h4 class="text-[11px] font-bold text-black uppercase tracking-widest mb-4">Daftar Agenda Terdekat</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($upcomingEvents as $event)
                            <div
                                class="flex items-center gap-4 p-3 rounded-xl bg-white border border-black/5 hover:border-primary/50 transition-all shadow-sm group">
                                <div
                                    class="w-12 h-12 rounded-lg bg-primary text-white flex flex-col items-center justify-center flex-shrink-0 shadow-md transform group-hover:scale-105 transition-transform">
                                    <span class="text-[10px] font-extrabold uppercase">{{ $event->start->format('M') }}</span>
                                    <span class="text-lg font-black">{{ $event->start->format('d') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-sm font-black text-black truncate">{{ $event->title }}</h5>
                                    <p class="text-[10px] text-black font-extrabold uppercase tracking-tighter opacity-70">
                                        {{ $event->start->format('H:i') }} • {{ $event->type }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-black/40 text-[11px] py-4 italic font-extrabold text-center col-span-2">Tidak ada
                                agenda minggu ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                        backgroundColor: [
                            '#ef4444', // Red for Used
                            '#22c55e'  // Green for Free
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide legend for small chart
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ' + context.raw.toFixed(2) + ' GB';
                                }
                            }
                        }
                    },
                    cutout: '65%',
                }
            });
        });
    </script>

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <script>
        // Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('digital-clock').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('date-display').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Mini Calendar
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('mini-calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                height: window.innerWidth < 768 ? 'auto' : 450,
                locale: 'id',
                events: @json($calendarEvents),
                dayMaxEvents: false,
                eventDisplay: 'none', // Don't show event bars, only dots
                dayCellDidMount: function (info) {
                    // Highlight days with events/holidays
                    const dateStr = info.date.toISOString().split('T')[0];
                    const eventsOnDay = @json($calendarEvents).filter(e => e.start.startsWith(dateStr));
                    const holiday = eventsOnDay.find(e => e.extendedProps && e.extendedProps.isHoliday);
                    const regularEvents = eventsOnDay.filter(e => !e.extendedProps || !e.extendedProps.isHoliday);

                    if (holiday) {
                        info.el.classList.add('holiday-bg');
                        const dayNumber = info.el.querySelector('.fc-daygrid-day-number');
                        if (dayNumber) {
                            dayNumber.style.color = '#dc2626';
                            dayNumber.style.fontWeight = '900';
                        }
                        info.el.title = holiday.title; // Tooltip
                    }

                    // Add color dots for events
                    if (regularEvents.length > 0) {
                        const dayTop = info.el.querySelector('.fc-daygrid-day-top');
                        if (dayTop) {
                            const dotsContainer = document.createElement('div');
                            dotsContainer.style.cssText = 'display:flex;flex-wrap:wrap;justify-content:center;gap:2px;margin-top:2px;';

                            regularEvents.slice(0, 3).forEach(event => {
                                const dot = document.createElement('div');
                                dot.style.cssText = `width:5px;height:5px;border-radius:50%;background-color:${event.extendedProps.categoryColor || event.backgroundColor}`;
                                dot.title = event.title;
                                dotsContainer.appendChild(dot);
                            });

                            if (regularEvents.length > 3) {
                                const moreDot = document.createElement('span');
                                moreDot.style.cssText = 'font-size:8px;color:#6b7280;font-weight:bold;';
                                moreDot.textContent = '+' + (regularEvents.length - 3);
                                dotsContainer.appendChild(moreDot);
                            }

                            dayTop.appendChild(dotsContainer);
                        }
                    }
                }
            });
            calendar.render();

            // Responsive resize
            window.addEventListener('resize', function () {
                if (window.innerWidth < 768) {
                    calendar.setOption('height', 'auto');
                } else {
                    calendar.setOption('height', 450);
                }
            });
        });
    </script>
@endsection