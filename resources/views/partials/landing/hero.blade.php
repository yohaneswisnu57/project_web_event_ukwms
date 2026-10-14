{{-- Banner utama. Tinggi gambar mengikuti rasio asli 16:9 (1920x1080). --}}
<section class="relative bg-[#efefef]"
         x-data="{
            active: 0,
            images: [
                '/images/hero-banner.webp'
            ]
         }">
    <div class="relative w-full overflow-hidden">
        <template x-for="(image, index) in images" :key="index">
            <div x-show="active === index"
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="w-full">
                <img :src="image"
                     width="1920" height="1080"
                     alt="{{ config('event.name') }} — {{ \Illuminate\Support\Carbon::parse(config('event.starts_at'))->locale('id')->translatedFormat('j F Y') }}, {{ config('event.venue.name') }} UKWMS"
                     fetchpriority="high"
                     class="wm-hero-in w-full h-auto object-contain">
            </div>
        </template>
    </div>
</section>
