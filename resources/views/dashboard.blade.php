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
        <div class="w-full lg:w-2/3 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex items-center gap-4">
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
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 flex items-center gap-4">
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
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500 relative overflow-hidden">
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
                            '#eab308', // Yellow for Used (matches card border)
                            '#f3f4f6'  // Gray for Free
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