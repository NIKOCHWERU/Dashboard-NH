<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard NH - Narasumber Hukum</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#D4AF37',
                        'primary-hover': '#B5952F',
                    }
                }
            }
        }
    </script>
    <style>
        .active-nav {
            background-color: #D4AF37;
            color: white;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="flex h-screen overflow-hidden">
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden" style="display: none;">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:static inset-y-0 left-0 z-30 bg-white border-r border-gray-200 flex flex-col transition-all duration-300 ease-in-out"
            :style="sidebarCollapsed ? 'width: 4rem' : 'width: 16rem'">
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200">
                <div class="flex items-center gap-3 transition-opacity duration-300"
                    :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    <img src="/images/logo.png" alt="Logo" class="h-11 w-11 object-contain"
                        onerror="this.src='https://ui-avatars.com/api/?name=NH&background=D4AF37&color=fff'">
                    <h1 class="text-xl font-bold text-primary whitespace-nowrap tracking-tight">Dashboard NH</h1>
                </div>
                <!-- Logo only when collapsed -->
                <div class="transition-opacity duration-300"
                    :class="sidebarCollapsed ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                    <img src="/images/logo.png" alt="Logo" class="h-10 w-10 object-contain mx-auto"
                        onerror="this.src='https://ui-avatars.com/api/?name=NH&background=D4AF37&color=fff'">
                </div>
                <!-- Close button for mobile -->
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-900 hover:text-black">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-2">
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center px-4 py-2.5 text-gray-900 font-bold hover:bg-gray-100 rounded-lg {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}"
                            :class="sidebarCollapsed ? 'justify-center transition-all' : ''">
                            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                            <span :class="sidebarCollapsed ? 'hidden' : 'block'">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('clients.index') }}"
                            class="flex items-center px-4 py-2.5 text-gray-900 font-bold hover:bg-gray-100 rounded-lg {{ request()->routeIs('clients.*') ? 'active-nav' : '' }}"
                            :class="sidebarCollapsed ? 'justify-center transition-all' : ''">
                            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            <span :class="sidebarCollapsed ? 'hidden' : 'block'">Klien</span>
                        </a>
                    </li>
                    <li x-data="{ open: {{ request()->routeIs('files.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="flex items-center w-full px-4 py-2.5 text-gray-900 font-bold hover:bg-gray-100 rounded-lg focus:outline-none"
                            :class="{ 'active-nav': {{ request()->routeIs('files.*') ? 'true' : 'false' }}, 'justify-center transition-all': sidebarCollapsed }">
                            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span :class="sidebarCollapsed ? 'hidden' : 'flex-1 text-left'">Berkas</span>
                            <svg :class="{ 'rotate-180': open, 'hidden': sidebarCollapsed }"
                                class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>
                        <!-- Sub-menu -->
                        <ul x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" class="space-y-1 mt-1"
                            :class="sidebarCollapsed ? 'hidden' : ''">
                            <li>
                                <a href="{{ route('files.index') }}"
                                    class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-700 hover:text-primary rounded-lg transition-colors {{ request()->fullUrlIs(route('files.index')) ? 'text-primary font-bold bg-yellow-50' : '' }}">
                                    Semua Berkas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('files.index', ['category' => 'Retainer']) }}"
                                    class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-700 hover:text-primary rounded-lg transition-colors {{ request()->fullUrlIs(route('files.index', ['category' => 'Retainer'])) ? 'text-primary font-bold bg-yellow-50' : '' }}">
                                    Retainer
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('files.index', ['category' => 'Perorangan']) }}"
                                    class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-700 hover:text-primary rounded-lg transition-colors {{ request()->fullUrlIs(route('files.index', ['category' => 'Perorangan'])) ? 'text-primary font-bold bg-yellow-50' : '' }}">
                                    Klien Perorangan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('files.index', ['category' => 'Kantor Narasumber Hukum']) }}"
                                    class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-700 hover:text-primary rounded-lg transition-colors {{ request()->fullUrlIs(route('files.index', ['category' => 'Kantor Narasumber Hukum'])) ? 'text-primary font-bold bg-yellow-50' : '' }}">
                                    Dokumen Internal
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('events.index') }}"
                            class="flex items-center px-4 py-2.5 text-gray-900 font-bold hover:bg-gray-100 rounded-lg {{ request()->routeIs('events.*') ? 'active-nav' : '' }}"
                            :class="sidebarCollapsed ? 'justify-center transition-all' : ''">
                            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span :class="sidebarCollapsed ? 'hidden' : 'block'">Kalender</span>
                        </a>
                    </li>
                    @if(auth()->user()->isAdmin())
                        <li>
                            <a href="{{ route('infos.index') }}"
                                class="flex items-center px-4 py-2.5 text-gray-900 font-bold hover:bg-gray-100 rounded-lg {{ request()->routeIs('infos.*') ? 'active-nav' : '' }}"
                                :class="sidebarCollapsed ? 'justify-center transition-all' : ''">
                                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                    </path>
                                </svg>
                                <span :class="sidebarCollapsed ? 'hidden' : 'block'">Info</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('users.index') }}"
                                class="flex items-center px-4 py-2.5 text-gray-900 font-bold hover:bg-gray-100 rounded-lg {{ request()->routeIs('users.*') ? 'active-nav' : '' }}"
                                :class="sidebarCollapsed ? 'justify-center transition-all' : ''">
                                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                                <span :class="sidebarCollapsed ? 'hidden' : 'block'">Karyawan</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center px-4 py-2.5 text-gray-900 font-bold hover:bg-gray-100 rounded-lg {{ request()->routeIs('profile.*') ? 'active-nav' : '' }}"
                            :class="sidebarCollapsed ? 'justify-center transition-all' : ''">
                            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span :class="sidebarCollapsed ? 'hidden' : 'block'">Profile</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="p-4 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2.5 text-gray-900 font-bold hover:bg-gray-100 rounded-lg"
                        :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span :class="sidebarCollapsed ? 'hidden' : 'block'">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm h-16 flex justify-between items-center px-6 flex-shrink-0">
                <!-- Hamburger/Toggle Button -->
                <button
                    @click="window.innerWidth < 1024 ? sidebarOpen = !sidebarOpen : sidebarCollapsed = !sidebarCollapsed"
                    class="text-gray-500 focus:outline-none focus:text-gray-700 hover:bg-gray-100 p-2 rounded-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="flex items-center ml-auto space-x-4">
                    <span class="text-gray-700 hidden sm:block">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=D4AF37&color=fff'">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=D4AF37&color=fff"
                            alt="Avatar" class="h-8 w-8 rounded-full">
                    @endif
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto">
                <div class="container mx-auto px-4 sm:px-6 py-8">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>