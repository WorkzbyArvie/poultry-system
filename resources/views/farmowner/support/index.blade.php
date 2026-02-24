@extends('farmowner.layouts.app')

@section('title', 'Support')
@section('header', 'Customer Support')
@section('subheader', 'Create tickets and chat with super admin')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-gray-800 border border-gray-700 rounded-lg p-6 h-fit">
        <h3 class="text-lg font-bold text-white mb-4">New Support Ticket</h3>
        <form action="{{ route('farmowner.support.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-300 mb-1">Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full rounded-lg bg-gray-900 border border-gray-600 text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1">Message</label>
                <textarea name="message" rows="5" required class="w-full rounded-lg bg-gray-900 border border-gray-600 text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold">Send to Super Admin</button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-gray-800 border border-gray-700 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-bold text-white">My Support Tickets</h3>
        </div>

        @if($tickets->count() > 0)
        <div class="divide-y divide-gray-700">
            @foreach($tickets as $ticket)
            <a href="{{ route('farmowner.support.show', $ticket) }}" class="block px-6 py-4 hover:bg-gray-700/50">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-semibold text-white">{{ $ticket->subject }}</p>
                        <p class="text-sm text-gray-400 mt-1">
                            {{ $ticket->latestMessage?->sender?->name ?? 'No messages yet' }} · {{ $ticket->updated_at->format('M d, Y h:i A') }}
                        </p>
                    </div>
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $ticket->status === 'open' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-300' }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>

        <div class="px-6 py-4 border-t border-gray-700">
            {{ $tickets->links() }}
        </div>
        @else
        <p class="text-gray-400 px-6 py-10 text-center">No support tickets yet.</p>
        @endif
    </div>
</div>
@endsection
