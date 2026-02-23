<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Dashboard NH</title>
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
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md text-center border border-gray-100">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 h-24 object-contain">
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Dashboard NH</h1>
        <p class="text-gray-400 text-sm font-medium mb-8 uppercase tracking-widest">Document Management System</p>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl relative mb-6 text-sm font-bold"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="mb-4">
            @csrf

            <div class="mb-4 text-left">
                <label for="email"
                    class="block text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Email
                    Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
            </div>

            <div class="mb-6 text-left">
                <label for="password"
                    class="block text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
            </div>

            <div class="flex items-center justify-between mb-4">
                <button type="submit"
                    class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-primary/20 transition-all">
                    Sign In
                </button>
            </div>
        </form>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">&copy; {{ date('Y') }} Dashboard NH
            </p>
        </div>
    </div>
</body>

</html>