<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1rem;" class="sm:gap-6 lg:gap-8">
        @if($isScanning)
            <div style="display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1.5rem;" class="lg:grid-cols-12 lg:gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                {{-- Scanner Section --}}
                <div class="lg:col-span-5 order-1">
                    <x-filament::section class="relative overflow-hidden border-none shadow-2xl rounded-[2rem] sm:rounded-[2.5rem]">
                        <x-slot name="heading">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="padding: 0.5rem; background: rgba(250, 196, 58, 0.1); border-radius: 0.5rem;">
                                    <x-heroicon-o-qr-code style="width: 1.25rem; height: 1.25rem; color: #FAC43A;" />
                                </div>
                                <span style="font-size: 1rem; font-weight: 900; letter-spacing: -0.025em; color: #0f172a;" class="sm:text-lg">QR Scanner</span>
                            </div>
                        </x-slot>

                        <div class="relative group mt-2">
                            {{-- Scanner Container with HUD --}}
                            <div style="position: relative; border-radius: 1.25rem; overflow: hidden; background: #020617; aspect-ratio: 1/1; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);" class="sm:rounded-[1.5rem]">
                                <div id="reader" style="width: 100%; height: 100%;"></div>
                                
                                {{-- HUD Overlays --}}
                                <div style="position: absolute; inset: 0; pointer-events: none; border: 12px solid rgba(0,0,0,0.2);" class="sm:border-[16px]"></div>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); border: 2px solid rgba(255,255,255,0.2); border-radius: 1rem;" class="scanner-hud">
                                    <div style="position: absolute; top: -4px; left: -4px; width: 1.25rem; height: 1.25rem; border-top: 4px solid #FAC43A; border-left: 4px solid #FAC43A; border-top-left-radius: 0.375rem;"></div>
                                    <div style="position: absolute; top: -4px; right: -4px; width: 1.25rem; height: 1.25rem; border-top: 4px solid #FAC43A; border-right: 4px solid #FAC43A; border-top-right-radius: 0.375rem;"></div>
                                    <div style="position: absolute; bottom: -4px; left: -4px; width: 1.25rem; height: 1.25rem; border-bottom: 4px solid #FAC43A; border-left: 4px solid #FAC43A; border-bottom-left-radius: 0.375rem;"></div>
                                    <div style="position: absolute; bottom: -4px; right: -4px; width: 1.25rem; height: 1.25rem; border-bottom: 4px solid #FAC43A; border-right: 4px solid #FAC43A; border-bottom-right-radius: 0.375rem;"></div>
                                </div>
                                {{-- Scan Animation --}}
                                <div style="position: absolute; top: 0; left: 0; right: 0; height: 2px; background: #FAC43A; box-shadow: 0 0 15px #FAC43A; animation: scan-line 4s linear infinite; opacity: 0.5;"></div>
                            </div>

                            <div id="camera-selector-container" style="margin-top: 1rem; display: none;">
                                <div style="position: relative;">
                                    <select id="camera-select" onchange="window.changeCamera(this.value)" style="width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 700; color: #1e293b; appearance: none; cursor: pointer; outline: none; transition: all 0.2s;">
                                        <option value="">Mendeteksi Kamera...</option>
                                    </select>
                                    <div style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8;">
                                        <x-heroicon-o-chevron-down style="width: 1rem; height: 1rem;" />
                                    </div>
                                </div>
                            </div>

                            <div id="scanner-status" style="margin-top: 1rem; padding: 0.75rem; border-radius: 0.75rem; background: #f8fafc; border: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: center; gap: 0.5rem;" class="sm:mt-4 sm:p-4 sm:gap-3">
                                <div style="position: relative; display: flex; height: 0.5rem; width: 0.5rem;" class="sm:h-2.5 sm:w-2.5">
                                    <span style="animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite; position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background: #fbbf24; opacity: 0.75;"></span>
                                    <span style="position: relative; display: inline-flex; border-radius: 9999px; height: 0.5rem; width: 0.5rem; background: #f59e0b;" class="sm:h-2.5 sm:w-2.5"></span>
                                </div>
                                <span id="status-text" style="font-size: 0.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.2em; color: #64748b;" class="sm:text-[0.5625rem]">Menghubungkan Kamera...</span>
                            </div>
                        </div>
                    </x-filament::section>
                </div>

                {{-- Recent History Section --}}
                <div class="lg:col-span-7 order-2">
                    <x-filament::section style="height: 100%; border-radius: 2rem; border: none;" class="sm:rounded-[2.5rem] shadow-sm">
                        <x-slot name="heading">
                            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="padding: 0.5rem; background: #f0fdf4; border-radius: 0.5rem;">
                                        <x-heroicon-o-check-badge style="width: 1.25rem; height: 1.25rem; color: #16a34a;" />
                                    </div>
                                    <span style="font-size: 1rem; font-weight: 900; letter-spacing: -0.025em; color: #0f172a;" class="sm:text-lg">Kehadiran Terkini</span>
                                </div>
                                <div style="font-size: 0.5rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; background: #f8fafc; padding: 0.25rem 0.6rem; border-radius: 9999px;" class="sm:text-[0.625rem] sm:px-3 sm:py-1">
                                    Live Updates
                                </div>
                            </div>
                        </x-slot>

                        <div style="margin-top: 1rem;" class="overflow-x-auto">
                            {{ $this->table }}
                        </div>
                    </x-filament::section>
                </div>
            </div>
        @else
            {{-- Detail View Section --}}
            <div style="width: 100%; max-width: 30rem; margin: 0 auto;" class="px-4 sm:px-0 animate-in zoom-in-95 fade-in slide-in-from-top-4 duration-500">
                <x-filament::section style="border-radius: 2rem; border: none; overflow: hidden;" class="sm:rounded-[3rem] shadow-2xl !p-0">
                    <div style="background: linear-gradient(to bottom right, #FAC43A, #f59e0b); padding: 2rem; text-align: center; position: relative;" class="sm:p-10">
                        <div style="position: absolute; inset: 0; opacity: 0.1; background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 16px 16px;"></div>
                        
                        <div style="position: relative; z-index: 10;">
                            <div style="width: 3.5rem; height: 3.5rem; background: rgba(255,255,255,0.2); backdrop-filter: blur(12px); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto; border: 2px solid rgba(255,255,255,0.4); box-shadow: 0 15px 20px -5px rgba(0,0,0,0.1);" class="sm:w-16 sm:h-16 sm:mb-4 sm:rounded-2xl">
                                <x-heroicon-o-user style="width: 1.5rem; height: 1.5rem; color: white;" class="sm:w-8 sm:h-8" />
                            </div>
                            <h2 style="font-size: 1.5rem; font-weight: 900; color: white; letter-spacing: -0.025em; line-height: 1.2;" class="sm:text-3xl leading-tight">{{ $scannedRegistration->attendees->first()->name ?? $scannedRegistration->invitation->name }}</h2>
                            <p style="margin-top: 0.4rem; font-size: 0.5rem; font-weight: 900; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 0.15em;" class="sm:text-[0.625rem] sm:mt-2 sm:tracking-[0.2em]">{{ $scannedRegistration->position ?? 'VIP GUEST' }}</p>
                        </div>
                    </div>

                    <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;" class="sm:p-10 sm:gap-8">
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;" class="sm:gap-4">
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; background: #f8fafc; border-radius: 1rem; border: 1px solid #f1f5f9;" class="sm:p-4 sm:gap-4">
                                <div style="width: 2rem; height: 2rem; background: white; border-radius: 0.6rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); color: #FAC43A;" class="sm:w-10 sm:h-10 sm:rounded-xl">
                                    <x-heroicon-o-building-office style="width: 1rem; height: 1rem;" class="sm:w-5 sm:h-5" />
                                </div>
                                <div>
                                    <p style="font-size: 0.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8;" class="sm:text-[0.5625rem]">Instansi</p>
                                    <p style="font-size: 0.875rem; font-weight: 700; color: #1e293b;" class="sm:text-base">{{ $scannedRegistration->invitation->name }}</p>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; background: #f8fafc; border-radius: 1rem; border: 1px solid #f1f5f9;" class="sm:p-4 sm:gap-4">
                                <div style="width: 2rem; height: 2rem; background: white; border-radius: 0.6rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); color: #FAC43A;" class="sm:w-10 sm:h-10 sm:rounded-xl">
                                    <x-heroicon-o-identification style="width: 1rem; height: 1rem;" class="sm:w-5 sm:h-5" />
                                </div>
                                <div>
                                    <p style="font-size: 0.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8;" class="sm:text-[0.5625rem]">Registration ID</p>
                                    <p style="font-size: 0.875rem; font-weight: 700; color: #1e293b;" class="sm:text-base">#{{ str_pad($scannedRegistration->id, 6, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </div>

                        @if($scannedRegistration->checked_in_at)
                            <div style="padding: 1rem; background: #fff1f2; border-radius: 1rem; border: 1px solid #ffe4e6; display: flex; align-items: center; gap: 0.75rem;" class="sm:p-5">
                                <x-heroicon-o-exclamation-triangle style="width: 1rem; height: 1rem; color: #f43f5e; flex-shrink: 0;" class="sm:w-5 sm:h-5" />
                                <p style="color: #9f1239; font-size: 0.75rem; font-weight: 700;" class="sm:text-sm">Sudah check-in pada pukul {{ $scannedRegistration->checked_in_at->format('H:i') }} WIB</p>
                            </div>
                        @endif

                        <div style="display: grid; grid-template-columns: 1fr; gap: 0.5rem;" class="sm:gap-3">
                            <x-filament::button 
                                wire:click="confirmPresence" 
                                color="gray"
                                size="xl"
                                style="width: 100%; min-height: 44px; padding: 0.875rem; background: #FAC43A; border-radius: 1rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.15em; box-shadow: 0 15px 20px -5px rgba(0,0,0,0.1);"
                                class="hover:!bg-[#FAC43A] hover:!text-black transition-all duration-300 sm:p-4 sm:tracking-[0.2em] sm:rounded-2xl cursor-pointer"
                            >
                                Konfirmasi Kehadiran
                            </x-filament::button>
                            
                            <x-filament::button 
                                wire:click="resetScan" 
                                color="gray" 
                                variant="link"
                                size="sm"
                                style="width: 100%; min-height: 44px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;"
                                class="cursor-pointer"
                            >
                                Batal & Scan Ulang
                            </x-filament::button>
                        </div>
                    </div>
                </x-filament::section>
            </div>
        @endif
    </div>

    @pushOnce('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        (function() {
            let html5QrCode;
            let selectedCameraId = null;
            
            window.changeCamera = (deviceId) => {
                selectedCameraId = deviceId;
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        startScanner();
                    }).catch(err => console.error(err));
                } else {
                    startScanner();
                }
            };

            const startScanner = () => {
                const readerElement = document.getElementById('reader');
                if (!readerElement) return;

                if (html5QrCode) { 
                    html5QrCode.clear(); 
                }

                html5QrCode = new Html5Qrcode("reader");
                
                // Calculate dynamic QR box size (70% of container width, max 300px)
                const containerWidth = readerElement.offsetWidth;
                const qrBoxSize = Math.min(containerWidth * 0.7, 300);
                
                const statusText = document.getElementById('status-text');

                // If no camera selected yet, get list and pick the best one
                if (!selectedCameraId) {
                    Html5Qrcode.getCameras().then(devices => {
                        if (devices && devices.length) {
                            const cameraSelect = document.getElementById('camera-select');
                            const selectorContainer = document.getElementById('camera-selector-container');
                            
                            if (cameraSelect) {
                                cameraSelect.innerHTML = '';
                                devices.forEach(device => {
                                    const option = document.createElement('option');
                                    option.value = device.id;
                                    option.text = device.label || `Camera ${cameraSelect.length + 1}`;
                                    cameraSelect.appendChild(option);
                                });
                                
                                selectorContainer.style.display = 'block';
                                
                                // Default to environment camera if possible
                                const backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment'));
                                selectedCameraId = backCamera ? backCamera.id : devices[0].id;
                                cameraSelect.value = selectedCameraId;
                            }
                            
                            initScanner(selectedCameraId, qrBoxSize);
                        }
                    }).catch(err => {
                        if (statusText) {
                            statusText.innerText = "ERROR: PERIZINAN KAMERA DITOLAK";
                            statusText.style.color = "#f43f5e";
                        }
                    });
                } else {
                    initScanner(selectedCameraId, qrBoxSize);
                }
            };

            const initScanner = (deviceId, qrBoxSize) => {
                const statusText = document.getElementById('status-text');
                
                html5QrCode.start(
                    deviceId,
                    { 
                        fps: 30,
                        qrbox: { width: qrBoxSize, height: qrBoxSize },
                        aspectRatio: 1.0
                    },
                    (decodedText) => {
                        html5QrCode.stop().then(() => { 
                            @this.checkIn(decodedText); 
                        });
                    },
                    () => {} 
                ).then(() => {
                    if (statusText) {
                        statusText.innerText = "SIAP PINDAI TIKET";
                        statusText.parentElement.style.background = "#f0fdf4";
                        statusText.style.color = "#16a34a";
                    }
                }).catch((err) => {
                    if (statusText) {
                        statusText.innerText = "ERROR: KAMERA SEDANG DIGUNAKAN";
                        statusText.style.color = "#f43f5e";
                    }
                });
            };

            document.addEventListener('livewire:initialized', () => {
                startScanner();
            });

            document.addEventListener('livewire:navigated', () => {
                if (html5QrCode) { 
                    html5QrCode.stop().catch(() => {}); 
                }
                setTimeout(startScanner, 500);
            });
        })();
    </script>
    @endpushOnce

    <style>
        @keyframes scan-line {
            0% { top: 15%; }
            50% { top: 85%; }
            100% { top: 15%; }
        }
        @keyframes ping {
            75%, 100% { transform: scale(2); opacity: 0; }
        }
        #reader__scan_region { background: transparent !important; }
        #reader video { border-radius: 1.25rem !important; object-fit: cover !important; }
        .fi-section { border-radius: 2rem !important; }
        .fi-ta-content { border-radius: 1rem !important; }
        .fi-ta-header { display: none !important; }
        
        /* Adjust HUD size to match dynamic qrbox */
        .scanner-hud {
            width: 70%;
            height: 70%;
            max-width: 300px;
            max-height: 300px;
        }

        @media (max-width: 640px) {
            .fi-section { border-radius: 1.5rem !important; }
        }
    </style>
</x-filament-panels::page>
