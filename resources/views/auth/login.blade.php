<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Artisan Hotel HMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-slate-950 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-slate-900/60 backdrop-blur-md border border-slate-800 rounded-2xl p-8 shadow-2xl">
        <!-- Brand -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-wider text-white">
                ARTISAN <span class="text-red-400 font-normal">HOTEL</span>
            </h1>
            <p class="text-xs text-slate-400 mt-2 uppercase tracking-widest">Property Management System</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                       class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 transition-colors">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400 transition-colors">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="mr-2 rounded bg-slate-800 border-slate-700 text-red-500 focus:ring-red-500 focus:ring-offset-slate-900">
                    Remember Me
                </label>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-white hover:bg-red-400 text-slate-950 font-semibold rounded-xl shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5">
                Sign In to System
            </button>
        </form>

        <!-- Demo Accounts Hint -->
        <div class="mt-8 pt-6 border-t border-slate-800/80 text-xs text-slate-500 text-center">
            <p class="font-medium mb-2 uppercase tracking-wider text-slate-400">Demo Logins (password: password)</p>
            <div class="grid grid-cols-2 gap-2 text-left mt-2">
                <p><strong>Admin:</strong> admin@artisan.com</p>
                <p><strong>Front Office:</strong> fo@artisan.com</p>
                <p><strong>Housekeeping:</strong> hk@artisan.com</p>
                <p><strong>F&B:</strong> fnb@artisan.com</p>
            </div>
        </div>
    </div>
</body>
</html>
