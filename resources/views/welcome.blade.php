<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Company Archive System</title>
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
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Archive System</h1>
        <p class="text-gray-500 mb-8">Company Internal Document Management</p>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="mb-4">
            @csrf

            <div class="mb-4 text-left">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6 text-left">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input id="password" type="password" name="password" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center justify-between mb-4">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Sign In
                </button>
            </div>
        </form>

        <div class="mt-6 border-t border-gray-200 pt-6">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Company Archive System</p>
        </div>
    </div>
</body>

</html>