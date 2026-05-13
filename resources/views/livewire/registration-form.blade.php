<div class="space-y-0 text-slate-800">
    @if($submitted)
        {{-- Success State --}}
        <section class="min-h-screen flex items-center justify-center p-6 bg-slate-50">
            <div class="max-w-xl w-full bg-white rounded-[3rem] p-12 text-center shadow-2xl border border-[#FAC43A]/20 animate-in fade-in zoom-in duration-700">
                <div class="w-24 h-24 bg-[#FAC43A] rounded-3xl mx-auto mb-8 flex items-center justify-center shadow-lg shadow-[#FAC43A]/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <h2 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">Registrasi Berhasil!</h2>
                <p class="text-slate-500 mb-8 leading-relaxed">Terima kasih atas konfirmasi kehadiran Anda. Admin akan memvalidasi data Anda dan mengirimkan QR code undangan ke email Anda.</p>
                
                <div class="bg-slate-50 border border-slate-100 rounded-3xl p-8 mb-8 text-left">
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-400 text-sm uppercase font-bold tracking-widest">ID Registrasi</span>
                            <span class="font-black text-slate-900">#{{ $latestRegistration->id ?? '---' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-400 text-sm uppercase font-bold tracking-widest">Nama Tamu</span>
                            <span class="font-black text-slate-900">{{ $latestRegistration->attendees->first()->name ?? '---' }}</span>
                        </div>
                    </div>
                </div>

                <a href="{{ url('/') }}" class="inline-block px-10 py-5 bg-[#FAC43A] text-white font-bold uppercase tracking-widest rounded-2xl hover:bg-[#e5b334] transition-all shadow-lg shadow-[#FAC43A]/30">
                    Kembali ke Beranda
                </a>
            </div>
        </section>
    @elseif($isClosed)
        {{-- Closed State --}}
        <section class="min-h-screen flex items-center justify-center p-6 bg-slate-50">
            <div class="max-w-xl w-full bg-white rounded-[3rem] p-12 text-center shadow-2xl border border-rose-100 animate-in fade-in zoom-in duration-700">
                <div class="w-24 h-24 bg-rose-500 rounded-3xl mx-auto mb-8 flex items-center justify-center shadow-lg shadow-rose-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h2 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">Registrasi Ditutup</h2>
                <p class="text-slate-500 mb-8 leading-relaxed">Mohon maaf, waktu registrasi untuk acara ini telah berakhir pada tanggal <b>13 Mei 2026 pukul 12.00 WIB</b>.</p>
                
                <a href="{{ url('/') }}" class="inline-block px-10 py-5 bg-slate-900 text-white font-bold uppercase tracking-widest rounded-2xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20">
                    Kembali ke Beranda
                </a>
            </div>
        </section>
    @else
        {{-- Landing Page + Form --}}
        
        {{-- Section 1: Hero --}}
        <section class="relative bg-white pt-16 md:pt-0" 
                 x-data="{ 
                    active: 0,
                    images: [
                        '/images/hero-banner.webp'
                    ]
                 }">
            
            {{-- Hero Background / Image Container --}}
            <div class="relative w-full overflow-hidden">
                <template x-for="(image, index) in images" :key="index">
                    <div x-show="active === index" 
                         x-transition:enter="transition ease-out duration-1000"
                         x-transition:enter-start="opacity-0 scale-105"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="w-full">
                        <img :src="image" class="w-full h-auto object-contain">
                    </div>
                </template>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-white/20"></div>
            </div>

            <div class="container mx-auto px-6 relative z-10 text-center py-8 md:py-16">
                <h1 class="text-3xl md:text-6xl font-black text-slate-900 leading-tight tracking-tighter mb-8 drop-shadow-sm">
                    SELAMAT DATANG<br><span class="text-[#FAC43A]">{{ Str::upper($invitation->name) }}</span>
                </h1>
                

                <a href="#register" class="px-12 py-5 bg-[#FAC43A] text-white font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-[#e5b334] transition-all shadow-xl shadow-[#FAC43A]/30 w-full sm:w-auto inline-block">
                    Konfirmasi Kehadiran
                </a>
            </div>
        </section>

        {{-- Section 2: Registration Form --}}
        <section id="register" class="py-32 bg-slate-50 scroll-mt-20">
            <div class="container mx-auto px-6">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-[3rem] shadow-xl overflow-hidden border border-slate-100">
                        <div class="p-12 md:p-16">
                            <div class="text-center mb-12">
                                <h2 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">Formulir Kehadiran</h2>
                                <i style="color: red;">*Undangan hanya berlaku untuk 1 orang*</i>
                                <p class="text-slate-500">Mohon lengkapi data diri Anda di bawah ini.</p>
                                <div class="w-12 h-1 bg-[#FAC43A] mx-auto rounded-full mt-4"></div>
                            </div>

                            <form wire:submit.prevent="submit" class="space-y-8">
                                {{-- Primary Attendee Details --}}
                                <div class="p-8 bg-slate-50 rounded-3xl border border-slate-100">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="space-y-2">
                                            <label class="text-xs font-bold uppercase tracking-widest text-slate-500 ml-1">Nama Lengkap</label>
                                            <input type="text" wire:model="attendees.0.name" 
                                                   class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-4 focus:border-[#FAC43A] focus:ring-0 transition-all outline-none"
                                                   placeholder="Masukkan nama lengkap...">
                                            @error('attendees.0.name') <span class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-bold uppercase tracking-widest text-slate-500 ml-1">Email</label>
                                            <input type="email" wire:model="attendees.0.email" 
                                                   class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-4 focus:border-[#FAC43A] focus:ring-0 transition-all outline-none"
                                                   placeholder="email@example.com">
                                            @error('attendees.0.email') <span class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-bold uppercase tracking-widest text-slate-500 ml-1">Nomor WhatsApp</label>
                                            <input type="text" wire:model="attendees.0.phone" 
                                                   class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-4 focus:border-[#FAC43A] focus:ring-0 transition-all outline-none"
                                                   placeholder="081234567890">
                                            @error('attendees.0.phone') <span class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-bold uppercase tracking-widest text-slate-500 ml-1">Jabatan / Instansi</label>
                                            <input type="text" wire:model="attendees.0.position" 
                                                   class="w-full bg-white border-2 border-slate-100 rounded-2xl px-6 py-4 focus:border-[#FAC43A] focus:ring-0 transition-all outline-none"
                                                   placeholder="Contoh: CEO di PT Maju Mundur">
                                            @error('attendees.0.position') <span class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-center pt-8">
                                    @error('registration_limit')
                                        <div class="bg-rose-50 border border-rose-200 text-rose-600 px-4 py-3 rounded-xl text-xs font-bold text-center mb-6 w-full">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <button type="submit" wire:loading.attr="disabled"
                                            class="w-full md:w-auto px-16 py-6 bg-[#FAC43A] text-white font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-[#e5b334] transition-all shadow-xl shadow-[#FAC43A]/30 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span wire:loading.remove>Konfirmasi Kehadiran</span>
                                        <span wire:loading>Memproses...</span>
                                    </button>
                                    <p class="text-slate-400 text-xs mt-6 font-bold uppercase tracking-widest italic">
                                        * Dengan mengklik tombol di atas, Anda menyatakan bersedia hadir dalam acara.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>        

        {{-- Footer --}}
        <footer class="py-12 bg-slate-50 border-t border-slate-200 text-center">
            <p class="text-[0.7rem] text-slate-400 uppercase tracking-widest font-bold">&copy; 2026 Universitas Katolik Widya Mandala Surabaya. All Rights Reserved.</p>
        </footer>
    @endif

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</div>
