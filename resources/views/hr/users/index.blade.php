<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR - Department Users</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-200">
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Department Users</h1>
                <p class="text-gray-400 text-sm">Manage role-based access by department</p>
            </div>
            <a href="{{ route('hr.users.create') }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold">+ Add User</a>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-900/40 border border-green-700 rounded-lg text-green-300">{{ session('success') }}</div>
        @endif

        <div class="bg-gray-800 border border-gray-700 rounded-lg overflow-hidden">
            @if($users->count() > 0)
            <table class="w-full text-sm">
                <thead class="bg-gray-700 border-b border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Department</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-700/60">
                        <td class="px-6 py-4 font-semibold text-white">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs bg-blue-900 text-blue-300">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs {{ $user->status === 'active' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-300' }}">{{ ucfirst($user->status ?? 'active') }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-400">{{ $user->created_at?->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-700">{{ $users->links() }}</div>
            @else
            <div class="px-6 py-10 text-center text-gray-400">No department users yet.</div>
            @endif
        </div>
    </div>
</body>
</html>
