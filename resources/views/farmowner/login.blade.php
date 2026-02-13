<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Owner Login - Poultry System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-orange-500">Poultry Farm</h1>
                <p class="text-gray-400 mt-2">Farm Owner Login</p>
            </div>

            @if ($errors->any())
            <div class="mb-6 p-4 bg-red-900/30 border border-red-700 rounded-lg">
                <p class="text-red-400 font-semibold mb-2">Login Failed</p>
                @foreach ($errors->all() as $error)
                <p class="text-red-300 text-sm">• {{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('farmowner.login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                           class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-600 rounded bg-gray-700">
                    <label for="remember" class="ml-2 text-sm text-gray-300">Remember me</label>
                </div>

                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 rounded-lg transition">
                    Login
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-400">Don't have an account? 
                    <a href="{{ route('farmowner.register') }}" class="text-orange-500 hover:text-orange-400 font-semibold">Register here</a>
                </p>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-300">Back to main login</a>
            </div>
        </div>

        <div class="mt-6 text-center text-gray-400">
            <p class="text-sm">Poultry System © 2026 | For Farm Owners Only</p>
        </div>
    </div>
</body>
</html>
