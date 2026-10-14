<x-mail::message>
# Pendaftaran Belum Dapat Diterima

Halo **{{ $participant->full_name }}**,

Mohon maaf, pendaftaran kamu untuk **RETINA 2026** belum dapat kami terima.

<x-mail::panel>
**Alasan:** {{ $participant->rejection_reason }}
</x-mail::panel>

Silakan mendaftar ulang dari awal melalui website pendaftaran dengan **alamat email yang berbeda** dari pendaftaran sebelumnya, lalu unggah bukti pembayaran yang sesuai.

Jika ada pertanyaan, silakan hubungi panitia.

Terima kasih,<br>
Panitia RETINA 2026
</x-mail::message>
