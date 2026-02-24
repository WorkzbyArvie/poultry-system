<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Poultry System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-200">
    <div class="max-w-5xl mx-auto px-6 py-10">
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-8">
            <h1 class="text-3xl font-bold text-white mb-2">{{ $title }}</h1>
            <p class="text-gray-400 mb-6">{{ $description }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($actions as $action)
                <a href="{{ $action['url'] }}" class="block p-4 bg-gray-900 border border-gray-700 rounded-lg hover:border-orange-500 hover:bg-gray-700/40 transition">
                    <p class="font-semibold text-orange-400">{{ $action['label'] }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ $action['hint'] }}</p>
                </a>
                @endforeach
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
