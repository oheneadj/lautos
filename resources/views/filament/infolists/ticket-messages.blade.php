{{-- Full message thread for one support ticket — oldest first, same order the customer sees it in. --}}
@php $messages = $ticket->messages()->with('user')->oldest()->get(); @endphp

@if ($messages->isEmpty())
    <p class="text-sm text-gray-500">No messages yet.</p>
@else
    <div class="space-y-3">
        @foreach ($messages as $message)
            <div class="rounded-lg border p-3 {{ $message->is_admin ? 'border-primary-200 bg-primary-50 dark:border-primary-800 dark:bg-primary-950' : 'border-gray-200 dark:border-gray-700' }}">
                <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                @if ($message->attachment_path)
                    @php
                        $attachmentUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                            'ticket-attachments.show',
                            now()->addMinutes(5),
                            ['message' => $message->uuid]
                        );
                    @endphp
                    <a href="{{ $attachmentUrl }}" target="_blank" class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:underline">
                        View Attachment
                    </a>
                @endif
                <p class="mt-1 text-xs text-gray-500">
                    {{ $message->is_admin ? 'Admin' : ($message->user?->name ?? 'Customer') }} · {{ $message->created_at->format('M j, Y g:ia') }}
                </p>
            </div>
        @endforeach
    </div>
@endif
