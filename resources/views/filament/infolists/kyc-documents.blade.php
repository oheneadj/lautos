{{-- Shows the Ghana Card and TIN documents inline (image) or as a download link (PDF) —
     always via a short-lived signed URL, never the raw storage path. --}}
@php
    $documents = [
        'ghana_card' => ['label' => 'Ghana Card', 'path' => $customer->ghana_card_path],
        'tin' => ['label' => 'TIN Document', 'path' => $customer->tin_path],
    ];
@endphp

<div class="grid grid-cols-2 gap-4">
    @foreach ($documents as $type => $document)
        <div>
            <p class="mb-1 text-sm font-medium">{{ $document['label'] }}</p>
            @if (empty($document['path']))
                <p class="text-sm text-gray-500">Not uploaded yet.</p>
            @else
                @php
                    $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                        'admin.kyc-documents.show',
                        now()->addMinutes(5),
                        ['user' => $customer->uuid, 'type' => $type]
                    );
                    $isImage = in_array(strtolower(pathinfo($document['path'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']);
                @endphp
                <a href="{{ $url }}" target="_blank" class="block rounded-lg border border-gray-200 p-2 dark:border-gray-700">
                    @if ($isImage)
                        <img src="{{ $url }}" class="h-40 w-full rounded object-cover" alt="{{ $document['label'] }}" />
                    @else
                        <div class="flex h-40 items-center justify-center rounded bg-gray-100 text-sm text-gray-500 dark:bg-gray-800">
                            Download PDF
                        </div>
                    @endif
                </a>
            @endif
        </div>
    @endforeach
</div>
