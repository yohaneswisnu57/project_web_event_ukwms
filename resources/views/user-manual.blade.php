@extends('layouts.guest')

@section('content')
<div class="bg-slate-50 min-h-screen text-slate-800 py-12">
    <div class="container mx-auto px-6 max-w-4xl">
        {{-- Header --}}
        <div class="text-center mb-16 animate-in fade-in slide-in-from-bottom-4 duration-700">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">System Documentation</h1>
            <p class="text-lg text-slate-500">Progress Report & User Manual untuk Event WIMATES LEADERS TALK</p>
            <div class="w-16 h-1 bg-[#FAC43A] mx-auto mt-6 rounded-full"></div>
        </div>

        {{-- Progress Report Section --}}
        <section class="mb-16 bg-white rounded-3xl shadow-sm border border-slate-100 p-8 md:p-12 animate-in fade-in slide-in-from-bottom-8 duration-700">
            <h2 class="text-3xl font-black text-slate-900 mb-8 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#FAC43A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                Laporan Progres Pengembangan
            </h2>
            
            <div class="space-y-6">
                <div class="border-l-4 border-[#FAC43A] pl-6 py-2">
                    <h3 class="text-xl font-bold text-slate-800 mb-2">1. Peningkatan Keamanan Tiket Masuk</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Tiket masuk berupa QR Code kini dibuat jauh lebih aman dan privat. Orang asing yang mencoba memindai (scan) tiket tamu sembarangan tidak akan bisa melihat informasi apapun atau menembus sistem panitia. Tiket tamu benar-benar hanya bisa dikenali dan dibaca oleh sistem pemindai khusus milik panitia.
                    </p>
                </div>
                
                <div class="border-l-4 border-[#FAC43A] pl-6 py-2">
                    <h3 class="text-xl font-bold text-slate-800 mb-2">2. Penyempurnaan Tampilan Website untuk HP</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Tampilan halaman depan dan formulir pendaftaran sudah dipercantik, terutama saat tamu membukanya dari layar handphone (HP). Gambar acara (banner) tampil pas dan penuh tanpa ada layar putih yang mengganggu. Foto para pembicara utama juga telah diperbesar agar terlihat lebih megah dan berwibawa.
                    </p>
                </div>

                <div class="border-l-4 border-[#FAC43A] pl-6 py-2">
                    <h3 class="text-xl font-bold text-slate-800 mb-2">3. Pembenahan Laporan Kehadiran (Dashboard)</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Papan informasi (Dashboard) untuk panitia telah disempurnakan agar datanya tidak membingungkan. Sekarang panitia bisa memantau dengan jelas: mana instansi yang sudah konfirmasi daftar, berapa total orang yang akan datang, dan berapa jumlah tamu yang benar-benar sudah tiba dan masuk ke lokasi acara.
                    </p>
                </div>

                <div class="border-l-4 border-[#FAC43A] pl-6 py-2">
                    <h3 class="text-xl font-bold text-slate-800 mb-2">4. Persiapan Sistem (Siap Pakai)</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Seluruh data uji coba yang sempat kita gunakan sebelumnya sudah dibersihkan secara total. Sistem kini sudah kembali bersih seperti baru, dan seluruh tautan pendaftaran siap disebarkan ke tamu dan undangan yang sesungguhnya.
                    </p>
                </div>
            </div>
        </section>

        {{-- User Manual Section --}}
        <section class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 md:p-12 animate-in fade-in slide-in-from-bottom-12 duration-1000">
            <h2 class="text-3xl font-black text-slate-900 mb-8 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#FAC43A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Panduan Pengguna (User Manual)
            </h2>

            <div class="space-y-10">
                {{-- Step 1 --}}
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-black text-xl text-slate-800">1</div>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-2">Admin: Membuat Undangan</h4>
                        <p class="text-slate-600 mb-4">Proses dimulai dari dashboard admin Filament.</p>
                        <ul class="list-disc pl-5 space-y-2 text-slate-600">
                            <li>Login ke halaman Admin (<code class="bg-slate-100 px-2 py-1 rounded text-sm text-pink-600">/admin</code>).</li>
                            <li>Masuk ke menu <strong>Invitations</strong>.</li>
                            <li>Klik tombol <strong>New Invitation</strong> atau gunakan fitur <strong>Import CSV</strong> untuk membuat undangan secara massal.</li>
                            <li>Setelah undangan berhasil dibuat, sistem akan menyediakan <em>Shareable Link</em> unik untuk tiap instansi.</li>
                        </ul>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-black text-xl text-slate-800">2</div>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-2">Admin: Distribusi Undangan</h4>
                        <p class="text-slate-600 mb-4">Mendistribusikan link secara personal kepada tamu VIP/Instansi terkait.</p>
                        <ul class="list-disc pl-5 space-y-2 text-slate-600">
                            <li>Salin link pendaftaran dari menu <strong>Invitations</strong>. Format link: <code class="bg-slate-100 px-2 py-1 rounded text-sm text-pink-600">/join/{kode-unik}</code>.</li>
                            <li>Kirimkan link tersebut melalui WhatsApp atau Email kepada perwakilan instansi.</li>
                        </ul>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-black text-xl text-slate-800">3</div>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-2">Tamu: Konfirmasi Kehadiran</h4>
                        <p class="text-slate-600 mb-4">Tamu membuka link dan mendaftarkan delegasinya.</p>
                        <ul class="list-disc pl-5 space-y-2 text-slate-600">
                            <li>Tamu membuka link, yang akan mengarahkan mereka ke form pendaftaran berdesain premium.</li>
                            <li>Mereka mengisi data diri (Nama, Email, No. HP, Jabatan).</li>
                            <li>Setelah menekan <strong>Konfirmasi Kehadiran</strong>, status undangan mereka di admin otomatis berubah menjadi "Sudah Digunakan".</li>
                        </ul>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-black text-xl text-slate-800">4</div>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-2">Admin: Validasi & Pengiriman Tiket</h4>
                        <p class="text-slate-600 mb-4">Langkah keamanan untuk menyaring pendaftar (Gated Delivery).</p>
                        <ul class="list-disc pl-5 space-y-2 text-slate-600">
                            <li>Admin memantau form masuk pada menu <strong>Registrations</strong>.</li>
                            <li>Jika data pendaftar sudah sesuai, Admin memilih pendaftar dan mengklik tombol aksi <strong>Kirim Tiket & Validasi</strong>.</li>
                            <li>Sistem akan secara otomatis di belakang layar (melalui Queue) mengirim email E-Ticket yang berisi <em>Secure QR Code</em> ke email tamu tersebut.</li>
                        </ul>
                    </div>
                </div>

                {{-- Step 5 --}}
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-[#FAC43A] text-white rounded-full flex items-center justify-center font-black text-xl">5</div>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-2">On-Site: Scan QR Kehadiran</h4>
                        <p class="text-slate-600 mb-4">Proses Check-in di hari H acara.</p>
                        <ul class="list-disc pl-5 space-y-2 text-slate-600">
                            <li>Panitia penerima tamu login ke aplikasi Admin di perangkat Mobile/Tablet.</li>
                            <li>Membuka menu <strong>Scan Kehadiran</strong> (<code class="bg-slate-100 px-2 py-1 rounded text-sm text-pink-600">/admin/scan-presence</code>).</li>
                            <li>Tamu menunjukkan E-Ticket QR Code dari ponsel mereka.</li>
                            <li>Panitia memindai QR Code menggunakan kamera perangkat.</li>
                            <li>Sistem memverifikasi "Secure Token" UUID. Jika valid dan belum dipakai, layar hijau "Check-in Berhasil" akan muncul. Statistik Dashboard "Tamu Sudah Hadir" akan otomatis bertambah secara Real-Time.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="text-center mt-12 mb-16">
            <a href="/" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#FAC43A] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
