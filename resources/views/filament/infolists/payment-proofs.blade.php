{{-- Shows each payment proof inline if it's an image, or as a download link if it's a PDF. --}}
@if ($order->paymentProofs->isEmpty())
    <p class="text-sm text-gray-500">No payment proofs uploaded yet.</p>
@else
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        @foreach ($order->paymentProofs as $proof)
            @php
                $isImage = in_array(strtolower(pathinfo($proof->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']);
                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($proof->file_path);
                $status = $proof->status ?? \App\Enums\PaymentProofStatus::Pending;
            @endphp
            <a href="{{ $url }}" target="_blank" class="block rounded-lg border border-gray-200 p-2 dark:border-gray-700">
                <div class="relative">
                    @if ($isImage)
                        <img src="{{ $url }}" class="h-32 w-full rounded object-cover" alt="Payment proof" />
                    @else
                        <div class="flex h-32 items-center justify-center rounded bg-gray-100 text-sm text-gray-500 dark:bg-gray-800">
                            Download PDF
                        </div>
                    @endif
                    <span @class([
                        'absolute top-1 right-1 rounded-full px-2 py-0.5 text-[10px] font-semibold text-white',
                        'bg-warning-500' => $status === \App\Enums\PaymentProofStatus::Pending,
                        'bg-success-500' => $status === \App\Enums\PaymentProofStatus::Accepted,
                        'bg-danger-500'  => $status === \App\Enums\PaymentProofStatus::Rejected,
                    ])>
                        {{ $status->label() }}
                    </span>
                </div>
                @if ($proof->note)
                    <p class="mt-1 text-xs text-gray-500">{{ $proof->note }}</p>
                @endif
            </a>
        @endforeach
    </div>
@endif
