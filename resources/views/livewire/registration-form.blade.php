<div>
    @if($submitted && $invitation->is_used && !$invitation->wasRecentlyCreated)
        {{-- Already registered before visiting this page --}}
        <div class="card already-registered">
            <div class="info-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
            </div>
            <h2>Sudah Terdaftar</h2>
            <p>Undangan atas nama <strong>{{ $invitation->name }}</strong> sudah digunakan untuk mendaftar sebelumnya.</p>
        </div>
    @elseif($submitted)
        {{-- Just submitted successfully --}}
        <div class="card success-card">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
            <h2>Pendaftaran Berhasil!</h2>
            <p>Terima kasih, <strong>{{ $invitation->name }}</strong>.<br>Data kehadiran Anda telah kami terima. Sampai jumpa di acara!</p>
        </div>
    @else
        {{-- Registration Form --}}
        <div class="card">
            <div class="card-header">
                <div class="badge">Event UKWMS</div>
                <h1>Form Pendaftaran Kehadiran</h1>
                <p class="company-name">{{ $invitation->name }}</p>
            </div>

            <div class="divider"></div>

            <form wire:submit="submit">
                <div class="form-group {{ $errors->has('attendance_count') ? 'has-error' : '' }}">
                    <label for="attendance_count">Jumlah yang Hadir</label>
                    <select id="attendance_count" wire:model.live="attendance_count">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} Orang</option>
                        @endfor
                    </select>
                    @error('attendance_count')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="attendees-list">
                    @foreach($attendees as $index => $attendee)
                        <div class="attendee-section" wire:key="attendee-{{ $index }}" style="margin-top: 1.5rem;">
                            <div class="section-title">
                                <span>Tamu #{{ $index + 1 }}</span>
                                @if($index === 0)
                                    <span class="main-badge">Utama</span>
                                @endif
                            </div>
                            
                            <div class="form-group {{ $errors->has('attendees.'.$index.'.name') ? 'has-error' : '' }}">
                                <label>Nama Lengkap</label>
                                <input type="text" wire:model="attendees.{{ $index }}.name" placeholder="Nama lengkap tamu">
                                @error('attendees.'.$index.'.name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="grid-2">
                                <div class="form-group {{ $errors->has('attendees.'.$index.'.email') ? 'has-error' : '' }}">
                                    <label>Email</label>
                                    <input type="email" wire:model="attendees.{{ $index }}.email" placeholder="email@contoh.com">
                                    @error('attendees.'.$index.'.email')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group {{ $errors->has('attendees.'.$index.'.phone') ? 'has-error' : '' }}">
                                    <label>Nomor HP</label>
                                    <input type="tel" wire:model="attendees.{{ $index }}.phone" placeholder="08xxxxxxxx">
                                    @error('attendees.'.$index.'.phone')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('attendees.'.$index.'.position') ? 'has-error' : '' }}">
                                <label>Jabatan</label>
                                <input type="text" wire:model="attendees.{{ $index }}.position" placeholder="Contoh: Manager, Direktur">
                                @error('attendees.'.$index.'.position')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(!$loop->last)
                                <div class="divider"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                    <span wire:loading wire:target="submit" class="spinner"></span>
                    <span wire:loading.remove wire:target="submit">Konfirmasi Kehadiran</span>
                    <span wire:loading wire:target="submit">Memproses...</span>
                </button>
            </form>
        </div>
    @endif
</div>
