<div class="flex flex-col items-center justify-center py-4">
    @php
        $qrCode = base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->generate($getRecord()->uuid));
    @endphp
    
    <div class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm">
        <img src="data:image/png;base64,{{ $qrCode }}" 
             alt="QR Code" 
             class="w-64 h-64 mx-auto">
    </div>

    @if($getRecord()->checked_in_at)
        <div class="mt-4 px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold uppercase tracking-wider border border-emerald-100 dark:border-emerald-500/20 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            Sudah Check-in
        </div>
    @endif
</div>
