@extends('layouts.guest')

@section('content')
<div class="space-y-0 text-slate-800">
    {{-- Section 1: Hero --}}
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

        <div class="container mx-auto px-6 relative z-10 text-center py-4 md:py-8">
            {{-- Registration button removed as requested --}}
        </div>
    </section>

    {{-- Section 2: Event Details - Removed as information is in the banner --}}

    {{-- Section 3: Agenda ditutup karena gak jelas --}}
    <!-- <section id="agenda" class="py-32 scroll-mt-20 bg-slate-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-20">
                <span class="text-[#FAC43A] font-bold uppercase tracking-[0.2em] text-xs">Timeline Acara</span>
                <h2 class="text-4xl md:text-5xl font-black mb-6 tracking-tight text-slate-900 mt-2">Agenda Utama</h2>
                <div class="w-12 h-1 bg-[#FAC43A] mx-auto rounded-full"></div>
            </div>
            
            <div class="max-w-4xl mx-auto space-y-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex gap-8 items-center hover:shadow-md transition-shadow">
                    <div class="text-[#FAC43A] font-black text-lg whitespace-nowrap">09:00</div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900">Registration & Morning Coffee</h4>
                        <p class="text-slate-500 text-sm mt-1">Sambut pagi dengan networking santai bersama rekan alumni.</p>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex gap-8 items-center hover:shadow-md transition-shadow">
                    <div class="text-[#FAC43A] font-black text-lg whitespace-nowrap">10:00</div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900">Opening & Keynote Session</h4>
                        <p class="text-slate-500 text-sm mt-1">Mendengarkan visi masa depan UKWMS dari Rektor dan Tokoh Alumni.</p>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex gap-8 items-center hover:shadow-md transition-shadow">
                    <div class="text-[#FAC43A] font-black text-lg whitespace-nowrap">11:30</div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900">Gala Lunch & Networking</h4>
                        <p class="text-slate-500 text-sm mt-1">Jamuan makan siang eksklusif sambil memperluas jejaring profesional.</p>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex gap-8 items-center hover:shadow-md transition-shadow">
                    <div class="text-[#FAC43A] font-black text-lg whitespace-nowrap">13:00</div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-900">Awarding & Closing</h4>
                        <p class="text-slate-500 text-sm mt-1">Penyerahan apresiasi kepada alumni berprestasi dan penutupan acara.</p>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    {{-- Section 4: Speakers --}}
    <section class="py-32 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-20">
                <!-- <span class="text-[#FAC43A] font-bold uppercase tracking-[0.2em] text-xs">Our Speakers</span> -->
                <h2 class="text-4xl font-black mb-4 tracking-tight text-slate-900 mt-2">Speakers</h2>
                <!-- <p class="text-slate-500 max-w-xl mx-auto">Menghadirkan sosok inspiratif yang telah memberikan kontribusi nyata bagi bangsa dan almamater.</p> -->
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto">
                <div class="group relative bg-slate-50 rounded-[3rem] p-10 hover:bg-slate-100 transition-all border border-slate-200/50 pt-32">
                    <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 rounded-[2.5rem] overflow-hidden border-4 border-white shadow-2xl rotate-2 group-hover:rotate-0 transition-transform bg-slate-200">
                        <img src="/images/pak jonan.png" 
                             alt="Ignatius Jonan" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="text-center">
                        <h4 class="text-3xl font-black text-slate-900">Ignatius Jonan</h4>
                        <p class="text-[#FAC43A] font-bold text-xs uppercase tracking-widest mt-1">Ketua Umum Yayasan Widya Mandala Surabaya</p>
                        <!-- <p class="text-slate-500 mt-6 text-sm leading-relaxed italic">"Kepemimpinan adalah teladan. Mari kita bangun masa depan yang lebih baik melalui integritas dan inovasi."</p> -->
                    </div>
                </div>
                <div class="group relative bg-slate-50 rounded-[3rem] p-10 hover:bg-slate-100 transition-all border border-slate-200/50 pt-32">
                    <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 rounded-[2.5rem] overflow-hidden border-4 border-white shadow-2xl -rotate-2 group-hover:rotate-0 transition-transform bg-slate-200">
                        <img src="/images/uskup surabaya.png" 
                             alt="Mgr. Agustinus Tri Budi Utomo" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="text-center">
                        <h4 class="text-3xl font-black text-slate-900">Mgr. Agustinus Tri Budi Utomo</h4>
                        <p class="text-[#FAC43A] font-bold text-xs uppercase tracking-widest mt-1">Uskup Keuskupan Surabaya</p>
                        <!-- <p class="text-slate-500 mt-6 text-sm leading-relaxed italic">"Dalam persaudaraan kita menemukan kekuatan. Mari merajut kembali tali silaturahmi sebagai satu keluarga besar."</p> -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-12 bg-slate-50 border-t border-slate-200 text-center">
        <p class="text-[0.7rem] text-slate-400 uppercase tracking-widest font-bold">&copy; 2026 Universitas Katolik Widya Mandala Surabaya. All Rights Reserved.</p>
    </footer>

    {{-- Smooth Scroll Script --}}
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
@endsection
