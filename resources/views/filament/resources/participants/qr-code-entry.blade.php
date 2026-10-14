<div class="flex flex-col items-center justify-center py-2">
    @php
        $qrCode = base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->generate($getRecord()->uuid));
    @endphp

    <div class="p-4 bg-white border border-gray-200 rounded-2xl shadow-sm">
        <img src="data:image/png;base64,{{ $qrCode }}"
             alt="QR Code"
             class="w-48 h-48 mx-auto">
    </div>
</div>
