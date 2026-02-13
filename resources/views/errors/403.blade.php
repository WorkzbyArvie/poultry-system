<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="text-6xl font-bold text-red-600 mb-4">403</div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Access Denied</h1>
            <p class="text-gray-600 mb-8">You don't have permission to access this resource. Please contact an administrator if you believe this is an error.</p>
            <a href="{{ route('dashboard') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                Go Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
