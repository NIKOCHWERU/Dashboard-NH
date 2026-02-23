@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Daftar Tugas</h1>
            <p class="text-sm text-gray-500 font-medium mt-1">Kelola tugas harian dengan Skala Prioritas Eisenhower</p>
        </div>
        <button onclick="document.getElementById('taskFormModal').classList.remove('hidden')" 
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl shadow-lg shadow-primary/20 text-sm transition-all hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Tugas
        </button>
    </div>

    <!-- Priority Legend -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-4 rounded-2xl border-l-4 border-red-500 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500 font-black">Q1</div>
            <div>
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest">Urgent & Important</p>
                <p class="text-[10px] text-gray-500">Kerjakan Sekarang</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border-l-4 border-amber-500 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-500 font-black">Q2</div>
            <div>
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest">Important, Not Urgent</p>
                <p class="text-[10px] text-gray-500">Jadwalkan</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border-l-4 border-blue-500 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 font-black">Q3</div>
            <div>
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest">Urgent, Not Important</p>
                <p class="text-[10px] text-gray-500">Delegasikan / Selesaikan Cepat</p>
            </div>
        </div>
    </div>

    @php
        $q1Tasks = $tasks->where('priority', 'Q1');
        $q2Tasks = $tasks->where('priority', 'Q2');
        $q3Tasks = $tasks->where('priority', 'Q3');
    @endphp

    <div class="space-y-8">
        @foreach(['Q1', 'Q2', 'Q3'] as $p)
            @php 
                $currTasks = $tasks->where('priority', $p);
                $colorClass = $p == 'Q1' ? 'red' : ($p == 'Q2' ? 'amber' : 'blue');
            @endphp
            
            <section>
                <div class="flex items-center gap-3 mb-4">
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ $p }} Priority</h2>
                    <div class="h-px flex-1 bg-gray-200"></div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @forelse($currTasks as $task)
                        <div class="group bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-6 h-6 rounded-lg border-2 border-gray-200 flex items-center justify-center group-hover:border-{{ $colorClass }}-500 transition-colors {{ $task->status == 'completed' ? 'bg-green-500 border-green-500' : '' }}">
                                        @if($task->status == 'completed')
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-bold {{ $task->status == 'completed' ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                                            {{ $task->title }}
                                        </h3>
                                        
                                        {{-- Real-time Timer Badge --}}
                                        <div x-data="{ 
                                            active: {{ $task->timer_started_at ? 'true' : 'false' }},
                                            total: {{ $task->total_seconds }},
                                            startedAt: {{ $task->timer_started_at ? 'new Date(\'' . $task->timer_started_at->toIso8601String() . '\').getTime()' : 'null' }},
                                            display: '00:00:00',
                                            updateDisplay() {
                                                let seconds = this.total;
                                                if (this.active && this.startedAt) {
                                                    seconds += Math.floor((Date.now() - this.startedAt) / 1000);
                                                }
                                                const h = Math.floor(seconds / 3600);
                                                const m = Math.floor((seconds % 3600) / 60);
                                                const s = seconds % 60;
                                                this.display = [h, m, s].map(v => v < 10 ? '0' + v : v).join(':');
                                            }
                                        }" x-init="updateDisplay(); if(active) setInterval(() => updateDisplay(), 1000)" 
                                        class="flex items-center gap-2 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold tracking-tight"
                                        :class="active ? 'bg-dark text-primary animate-pulse' : 'bg-gray-100 text-gray-500'">
                                            <svg class="w-3 h-3" :class="active ? 'animate-spin-slow' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="display"></span>
                                        </div>
                                    </div>
                                    @if($task->due_date)
                                        <p class="text-[10px] font-bold text-gray-400 uppercase mt-0.5">Deadline: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                {{-- Timer Controls --}}
                                @if($task->timer_started_at)
                                    <form action="{{ route('tasks.stop', $task) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-100 transition-all border border-red-100">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                <rect x="6" y="6" width="12" height="12" rx="2" />
                                            </svg>
                                            Stop
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('tasks.start', $task) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-primary/5 text-primary rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary/10 transition-all border border-primary/10">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z" />
                                            </svg>
                                            Start
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete Button (formerly in group-hover) --}}
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-300 hover:text-red-500 rounded-lg hover:bg-red-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bg-gray-50/50 border border-dashed border-gray-200 p-6 rounded-2xl text-center">
                            <p class="text-xs text-gray-400 font-medium italic">Tidak ada tugas untuk prioritas ini.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</div>

<!-- Add Task Modal -->
<div id="taskFormModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('taskFormModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="bg-white px-8 pt-8 pb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight" id="modal-title">Tambah Tugas Baru</h3>
                        <button type="button" onclick="document.getElementById('taskFormModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Judul Tugas</label>
                            <input type="text" name="title" required placeholder="Apa yang harus diselesaikan?" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Skala Prioritas</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority" value="Q1" class="peer hidden" required>
                                    <div class="text-center p-3 rounded-xl border-2 border-gray-100 peer-checked:border-red-500 peer-checked:bg-red-50 transition-all text-xs font-black text-gray-400 peer-checked:text-red-600">Q1</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority" value="Q2" class="peer hidden" checked>
                                    <div class="text-center p-3 rounded-xl border-2 border-gray-100 peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all text-xs font-black text-gray-400 peer-checked:text-amber-600">Q2</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority" value="Q3" class="peer hidden">
                                    <div class="text-center p-3 rounded-xl border-2 border-gray-100 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all text-xs font-black text-gray-400 peer-checked:text-blue-600">Q3</div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tenggat Waktu (Opsional)</label>
                            <input type="date" name="due_date" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm font-semibold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan Tambahan</label>
                            <textarea name="description" rows="3" placeholder="Detail opsional..."
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm font-semibold resize-none"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-primary hover:bg-primary-hover text-white font-black rounded-xl shadow-lg shadow-primary/20 text-sm transition-all hover:-translate-y-0.5">
                        Simpan Tugas
                    </button>
                    <button type="button" onclick="document.getElementById('taskFormModal').classList.add('hidden')" class="w-full sm:w-auto px-8 py-3 text-sm font-bold text-gray-400 hover:text-gray-600">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
