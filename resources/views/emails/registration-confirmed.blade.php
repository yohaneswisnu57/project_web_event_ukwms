<x-mail::message>
# Konfirmasi Pendaftaran Berhasil

Halo **{{ $registration->invitation->name }}**,

Terima kasih telah melakukan konfirmasi kehadiran untuk **WIMATES LEADERS TALK**. Data pendaftaran Anda telah kami terima.

Silakan simpan **Invitation QR Code** di bawah ini untuk ditunjukkan kepada panitia saat check-in di lokasi acara:

<div style="text-align: center; margin: 30px 0; background-color: #f8fafc; padding: 40px; border-radius: 30px; border: 1px solid #e2e8f0;">
    <div style="margin-bottom: 10px;">
        <img src="{{ $message->embedData($qrCode, 'qrcode.png', 'image/png') }}" 
             alt="Invitation QR Code" 
             style="width: 200px; height: 200px; border: 4px solid #e2e8f0; border-radius: 20px;">
    </div>
    <p style="font-family: monospace; font-size: 14px; color: #1e293b; font-weight: bold; margin-top: 10px;">REG-ID: {{ str_pad($registration->id, 6, '0', STR_PAD_LEFT) }}</p>
</div>

<x-mail::panel>
**Detail Acara:**
- **Tanggal:** 16 Mei 2026
- **Waktu:** 09:00 - 15:00 WIB
- **Lokasi:** Auditorium UKWMS, Jl. Dinoyo No. 42-44, Surabaya.
</x-mail::panel>

<x-mail::button :url="url('/')" color="success">
Kunjungi Website Event
</x-mail::button>

Sampai jumpa di lokasi acara!

Terima kasih,<br>
Panitia WIMATES LEADERS TALK
</x-mail::message>
