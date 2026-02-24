@extends('farmowner.layouts.app')

@section('title', 'Support Chat')
@section('header', 'Support Chat')
@section('subheader', 'Ticket #' . $ticket->id . ' · ' . $ticket->subject)

@section('content')
<div class="mb-4">
    <a href="{{ route('farmowner.support.index') }}" class="text-orange-400 hover:text-orange-300 text-sm">← Back to Support</a>
</div>

<div class="bg-gray-800 border border-gray-700 rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
        <div>
            <p class="font-semibold text-white">{{ $ticket->subject }}</p>
            <p class="text-sm text-gray-400">Status: {{ ucfirst($ticket->status) }}</p>
        </div>
        <span class="px-2 py-1 rounded text-xs font-semibold {{ $ticket->status === 'open' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-300' }}">
            {{ ucfirst($ticket->status) }}
        </span>
    </div>

    <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto bg-gray-900/40">
        @forelse($ticket->messages as $message)
        <div class="{{ $message->sender_role === 'farm_owner' ? 'text-right' : '' }}">
            <div class="inline-block max-w-2xl px-4 py-3 rounded-lg {{ $message->sender_role === 'farm_owner' ? 'bg-orange-600 text-white' : 'bg-gray-700 text-gray-200' }}">
                <p class="text-sm font-semibold mb-1">{{ $message->sender->name }} ({{ $message->sender_role === 'farm_owner' ? 'You' : 'Super Admin' }})</p>
                <p class="text-sm">{{ $message->message }}</p>
                <p class="text-xs mt-2 opacity-80">{{ $message->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
        @empty
        <p class="text-gray-400">No messages yet.</p>
        @endforelse
    </div>

    @if($ticket->status === 'open')
    <form action="{{ route('farmowner.support.reply', $ticket) }}" method="POST" class="p-6 border-t border-gray-700 bg-gray-800">
        @csrf
        <label class="block text-sm text-gray-300 mb-2">Reply</label>
        <textarea name="message" rows="4" required class="w-full rounded-lg bg-gray-900 border border-gray-600 text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
        <div class="mt-3 text-right">
            <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold">Send Message</button>
        </div>
    </form>
    @else
    <div class="p-6 border-t border-gray-700 text-sm text-gray-400">This ticket is closed.</div>
    @endif
</div>
@endsection
