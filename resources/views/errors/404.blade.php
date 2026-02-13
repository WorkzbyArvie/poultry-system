<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="text-6xl font-bold text-gray-600 mb-4">404</div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Page Not Found</h1>
            <p class="text-gray-600 mb-8">The resource you're looking for doesn't exist or has been removed.</p>
            <a href="{{ route('dashboard') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                Go Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
