<x-mail::message>
# Konfirmasi Pendaftaran Berhasil

Halo **{{ $registration->invitation->name }}**,

Terima kasih telah melakukan konfirmasi kehadiran untuk **Event UKWMS**. Data pendaftaran Anda telah kami terima dengan detail sebagai berikut:

- **Jumlah Tamu:** {{ $registration->attendance_count }} Orang
- **Waktu Daftar:** {{ $registration->created_at->format('d M Y H:i') }}

Silakan simpan **QR Code** di bawah ini untuk ditunjukkan kepada panitia saat check-in di lokasi acara:

<div style="text-align: center; margin: 30px 0;">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('admin.check-in', $registration->id)) }}" 
         alt="QR Code Check-in" 
         style="width: 200px; height: 200px; border: 10px solid #f3f4f6; border-radius: 20px;">
</div>

<x-mail::panel>
**Penting:** Harap membawa QR Code ini (digital atau cetak) untuk mempercepat proses masuk ke lokasi acara.
</x-mail::panel>

Sampai jumpa di acara kami!

Terima kasih,<br>
Panitia Event UKWMS
</x-mail::message>
