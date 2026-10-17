<x-mail::message>
# Pembayaran Terverifikasi

Halo **{{ $participant->full_name }}**,

Pembayaran pendaftaran **RETINA 2026 — Redaction Training in Art** sudah kami verifikasi. Kamu resmi terdaftar sebagai peserta!

Simpan **QR Code** di bawah ini dan tunjukkan kepada panitia saat registrasi ulang di lokasi acara:

<div style="text-align: center; margin: 30px 0; background-color: #f8fafc; padding: 40px; border-radius: 30px; border: 1px solid #e2e8f0;">
    <div style="margin-bottom: 10px;">
        <img src="{{ $message->embedData($qrCode, 'qrcode.png', 'image/png') }}"
             alt="QR Code Tiket"
             style="width: 200px; height: 200px; border: 4px solid #e2e8f0; border-radius: 20px;">
    </div>
    <p style="font-family: monospace; font-size: 14px; color: #1e293b; font-weight: bold; margin-top: 10px;">{{ $participant->code }}</p>
</div>

<x-mail::panel>
**Detail Acara:**
- **Tanggal:** Sabtu, 31 Oktober 2026
- **Waktu:** 09.00 WIB
- **Lokasi:** Ruang Teater Barat, Tower Barat Lt. 1, Universitas Katolik Widya Mandala Surabaya, Jl. Kalisari Selatan 1
</x-mail::panel>

@if (filled(config('retina.whatsapp_group_url')))
Untuk informasi dan pembaruan terbaru seputar acara, silakan bergabung ke grup WhatsApp peserta:

<x-mail::button :url="config('retina.whatsapp_group_url')" color="success">
Gabung Grup WhatsApp
</x-mail::button>
@endif

So, are you ready to **SPEAK UP, STAND OUT, and MAKE AN IMPACT?**

Sampai jumpa,<br>
Panitia RETINA 2026
</x-mail::message>
