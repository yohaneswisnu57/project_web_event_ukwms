<div>
    @if($submitted && $invitation->is_used && !$invitation->wasRecentlyCreated)
        {{-- Already registered before visiting this page --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] p-10 shadow-2xl text-center animate-in fade-in slide-in-from-bottom-8 duration-700">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Sudah Terdaftar</h2>
            <p class="text-slate-400 leading-relaxed mb-8">Undangan atas nama <strong class="text-slate-200">{{ $invitation->name }}</strong> sudah digunakan untuk mendaftar sebelumnya.</p>
            
            {{-- QR Code Section --}}
            @if($latestRegistration)
                <div class="bg-white p-4 rounded-3xl inline-block mb-6 shadow-2xl border-4 border-blue-500/20">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('admin.check-in', $latestRegistration->id)) }}" 
                         alt="QR Code Check-in" 
                         class="w-48 h-48 sm:w-56 sm:h-56">
                </div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Gunakan QR Code Ini Untuk Check-in</p>
            @endif
        </div>
    @elseif($submitted)
        {{-- Just submitted successfully --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] p-10 shadow-2xl text-center animate-in fade-in zoom-in duration-700">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Pendaftaran Berhasil!</h2>
            <p class="text-slate-400 leading-relaxed mb-8">Terima kasih, <strong class="text-slate-200">{{ $invitation->name }}</strong>.<br>Data kehadiran Anda telah kami terima.</p>
            
            {{-- QR Code Section --}}
            @if($latestRegistration)
                <div class="bg-white p-4 rounded-3xl inline-block mb-6 shadow-2xl shadow-emerald-500/10 border-4 border-emerald-500/20">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('admin.check-in', $latestRegistration->id)) }}" 
                         alt="QR Code Check-in" 
                         class="w-48 h-48 sm:w-56 sm:h-56">
                </div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Silakan Simpan/Screenshot QR Code Ini<br>Untuk Check-in di Lokasi Acara</p>
            @endif
        </div>
    @else
        {{-- Registration Form --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] p-8 sm:p-10 shadow-2xl animate-in fade-in slide-in-from-bottom-8 duration-700">
            <div class="text-center mb-8">
                <div class="inline-block bg-gradient-to-br from-amber-400 to-amber-600 text-slate-900 px-4 py-1 rounded-full text-[0.7rem] font-bold uppercase tracking-[0.15em] mb-4 shadow-lg shadow-amber-500/20">
                    Event UKWMS
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold bg-gradient-to-br from-white to-indigo-200 bg-clip-text text-transparent leading-tight">
                    Konfirmasi Kehadiran
                </h1>
                <p class="text-slate-400 mt-2 font-medium">{{ $invitation->name }}</p>
            </div>

            <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-8"></div>

            <form wire:submit="submit" class="space-y-6">
                {{-- Attendance Count --}}
                <div class="space-y-2">
                    <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest ml-1">Jumlah yang Hadir</label>
                    <div class="relative group">
                        <select wire:model.live="attendance_count" 
                            class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white text-base appearance-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all cursor-pointer group-hover:bg-white/[0.08]">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" class="bg-[#1a1a2e] text-white">{{ $i }} Orang</option>
                            @endfor
                        </select>
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    @error('attendance_count')
                        <div class="text-rose-400 text-[0.75rem] mt-1.5 flex items-center gap-1 ml-1">
                            <span class="text-sm">⚠</span> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Attendees List --}}
                <div class="space-y-8 pt-2">
                    @foreach($attendees as $index => $attendee)
                        <div wire:key="attendee-{{ $index }}" class="space-y-5 animate-in fade-in duration-500">
                            <div class="flex items-center justify-between ml-1">
                                <span class="text-[0.65rem] font-black text-slate-500 uppercase tracking-[0.2em]">Tamu #{{ $index + 1 }}</span>
                                @if($index === 0)
                                    <span class="bg-amber-500/10 text-amber-500 text-[0.6rem] font-bold px-2 py-0.5 rounded uppercase tracking-wider border border-amber-500/20">Pendaftar Utama</span>
                                @endif
                            </div>
                            
                            {{-- Name --}}
                            <div class="space-y-2">
                                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                <input type="text" wire:model="attendees.{{ $index }}.name" placeholder="Masukkan nama lengkap"
                                    class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder:text-white/30 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all">
                                @error('attendees.'.$index.'.name')
                                    <div class="text-rose-400 text-[0.75rem] mt-1 flex items-center gap-1 ml-1">
                                        <span class="text-sm">⚠</span> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                {{-- Email --}}
                                <div class="space-y-2">
                                    <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest ml-1">Email</label>
                                    <input type="email" wire:model="attendees.{{ $index }}.email" placeholder="email@contoh.com"
                                        class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder:text-white/30 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all">
                                    @error('attendees.'.$index.'.email')
                                        <div class="text-rose-400 text-[0.75rem] mt-1 flex items-center gap-1 ml-1">
                                            <span class="text-sm">⚠</span> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Phone --}}
                                <div class="space-y-2">
                                    <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor HP</label>
                                    <input type="tel" wire:model="attendees.{{ $index }}.phone" placeholder="08xxxxxxxx"
                                        class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder:text-white/30 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all">
                                    @error('attendees.'.$index.'.phone')
                                        <div class="text-rose-400 text-[0.75rem] mt-1 flex items-center gap-1 ml-1">
                                            <span class="text-sm">⚠</span> {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Position --}}
                            <div class="space-y-2">
                                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest ml-1">Jabatan</label>
                                <input type="text" wire:model="attendees.{{ $index }}.position" placeholder="Contoh: Manager, Direktur"
                                    class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder:text-white/30 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all">
                                @error('attendees.'.$index.'.position')
                                    <div class="text-rose-400 text-[0.75rem] mt-1 flex items-center gap-1 ml-1">
                                        <span class="text-sm">⚠</span> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            @if(!$loop->last)
                                <div class="h-px bg-white/5 mt-8"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full group relative overflow-hidden bg-gradient-to-br from-amber-400 to-amber-600 p-4 rounded-2xl text-slate-900 font-bold uppercase tracking-wider hover:shadow-[0_0_30px_-5px_rgba(245,158,11,0.5)] transition-all active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none" 
                        wire:loading.attr="disabled">
                        <div class="flex items-center justify-center gap-3">
                            <div wire:loading wire:target="submit" class="w-5 h-5 border-2 border-slate-900/30 border-t-slate-900 rounded-full animate-spin"></div>
                            <span wire:loading.remove wire:target="submit">Konfirmasi Kehadiran</span>
                            <span wire:loading wire:target="submit">Memproses...</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
