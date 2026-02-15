@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Karyawan</h2>
            <p class="text-sm text-gray-500">Kelola data karyawan dan hak akses.</p>
        </div>
        <a href="{{ route('users.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-bold rounded-lg transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Karyawan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($users as $user)
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center hover:shadow-md transition-shadow group">
                <div class="relative">
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=D4AF37&color=fff' }}"
                        alt="{{ $user->name }}"
                        class="w-24 h-24 rounded-full object-cover mb-4 border-4 border-gray-50 group-hover:border-primary/20 transition-colors"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=D4AF37&color=fff'">
                    <span class="absolute bottom-4 right-0 w-5 h-5 bg-green-500 border-2 border-white rounded-full"
                        title="Active"></span>
                </div>

                <h3 class="font-bold text-gray-900 text-lg mb-1 line-clamp-1">{{ $user->name }}</h3>
                <p class="text-xs text-gray-500 mb-3 truncate w-full px-2" title="{{ $user->email }}">{{ $user->email }}</p>

                <span
                    class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider rounded-full {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }} mb-6">
                    {{ ucfirst($user->role) }}
                </span>

                <div class="grid grid-cols-{{ auth()->user()->isAdmin() ? '3' : '1' }} gap-2 w-full mt-auto">
                    <a href="mailto:{{ $user->email }}?subject=Pesan%20dari%20Dashboard"
                        class="flex items-center justify-center bg-blue-50 text-blue-600 py-2 rounded-lg font-bold text-sm hover:bg-blue-100 transition-colors"
                        title="Kirim Email">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('users.edit', $user) }}"
                            class="flex items-center justify-center bg-yellow-50 text-yellow-600 py-2 rounded-lg font-bold text-sm hover:bg-yellow-100 transition-colors"
                            title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                </path>
                            </svg>
                        </a>

                        @if(auth()->id() !== $user->id)
                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this employee?');" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full flex items-center justify-center bg-red-50 text-red-600 py-2 rounded-lg font-bold text-sm hover:bg-red-100 transition-colors"
                                    title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <button disabled
                                class="w-full flex items-center justify-center bg-gray-50 text-gray-300 py-2 rounded-lg font-bold text-sm cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $users->links() }}
    </div>
@endsection