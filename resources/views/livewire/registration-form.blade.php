@use('App\Models\Participant')
@use('Illuminate\Support\Number')

@php
    $fee = Number::currency(Participant::REGISTRATION_FEE, 'IDR', 'id', 0);
@endphp

<div>
    <header class="r-hero r-page-head">
        @include('partials.retina.nav', ['showCta' => false])

        <div class="r-container">
            <div class="r-page-head__body">
                <a class="r-back r-rise r-d1" href="{{ route('home') }}"><span aria-hidden="true">←</span> Kembali ke beranda</a>
                <h1 class="r-rise r-d2">Formulir pendaftaran</h1>
                <p class="r-rise r-d3">Isi data dirimu, transfer {{ $fee }}, lalu unggah buktinya. Tiket QR dikirim ke email setelah panitia memverifikasi.</p>
            </div>
        </div>
    </header>

    <main id="konten" class="r-container r-form-layout">
        <div class="r-form-main">
            @if ($registeredCode)
                <section class="r-card r-form-card r-state r-pop" aria-live="polite">
                    <div class="r-state__icon" aria-hidden="true">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                    <h2>Pendaftaran diterima.</h2>
                    <p>Terima kasih! Panitia akan memeriksa bukti transfermu. Hasil verifikasi beserta tiket QR dikirim ke <strong>{{ $registeredEmail }}</strong> — cek juga folder spam.</p>
                    <div class="r-state__code">
                        <span class="r-ticket__label">Kode pendaftaran</span>
                        <strong>{{ $registeredCode }}</strong>
                    </div>
                    <p>Simpan kode ini bila perlu menghubungi panitia.</p>
                    <div class="r-state__actions">
                        <a class="r-btn r-btn--brick" href="{{ route('home') }}">Kembali ke beranda</a>
                    </div>
                </section>
            @elseif (! $isRegistrationOpen)
                <section class="r-card r-form-card r-state r-pop">
                    <div class="r-state__icon" aria-hidden="true">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                    </div>
                    <h2>Pendaftaran ditutup.</h2>
                    <p>
                        @if ($remainingQuota === 0)
                            Kuota peserta {{ config('retina.name') }} sudah terpenuhi.
                        @else
                            Batas waktu pendaftaran {{ config('retina.name') }} sudah lewat.
                        @endif
                        Terima kasih atas antusiasmenya — sampai jumpa di kesempatan berikutnya.
                    </p>
                    <div class="r-state__actions">
                        <a class="r-btn r-btn--brick" href="{{ route('home') }}">Kembali ke beranda</a>
                    </div>
                </section>
            @else
                <form wire:submit="register" class="r-card r-form-card r-rise r-d3" novalidate>
                    @error('form')
                        <p class="r-alert" role="alert" style="margin-bottom: 28px">{{ $message }}</p>
                    @enderror

                    <fieldset class="r-fieldset">
                        <legend><span class="r-mono">01</span><span class="r-display">Data diri</span></legend>
                        <div class="r-grid">
                            @include('partials.retina.field', ['name' => 'full_name', 'label' => 'Nama lengkap', 'autocomplete' => 'name', 'hint' => 'Ditulis persis seperti yang ingin dicetak di sertifikat.', 'full' => true, 'maxlength' => 255])
                            @include('partials.retina.field', ['name' => 'email', 'label' => 'Email aktif', 'type' => 'email', 'autocomplete' => 'email', 'placeholder' => 'nama@email.com', 'hint' => 'Tiket QR dikirim ke alamat ini.', 'maxlength' => 255])
                            @include('partials.retina.field', ['name' => 'whatsapp', 'label' => 'Nomor WhatsApp', 'type' => 'tel', 'autocomplete' => 'tel', 'inputmode' => 'tel', 'placeholder' => '081234567890', 'maxlength' => 20])
                            @include('partials.retina.field', ['name' => 'food_allergy', 'label' => 'Alergi makanan', 'optional' => true, 'placeholder' => 'Kosongkan jika tidak ada', 'full' => true, 'maxlength' => 255])
                        </div>
                    </fieldset>

                    <fieldset class="r-fieldset">
                        <legend><span class="r-mono">02</span><span class="r-display">Data akademik</span></legend>
                        <div class="r-grid">
                            <div class="r-field r-col-full" role="radiogroup" aria-labelledby="origin-label">
                                <span id="origin-label" class="r-label">Asal kampus</span>
                                <div class="r-segment">
                                    <label>
                                        <input type="radio" name="origin" value="internal" wire:model.live="origin">
                                        <span><strong>Mahasiswa UKWMS</strong><small>Universitas Katolik Widya Mandala Surabaya</small></span>
                                    </label>
                                    <label>
                                        <input type="radio" name="origin" value="external" wire:model.live="origin">
                                        <span><strong>Kampus lain</strong><small>Peserta eksternal</small></span>
                                    </label>
                                </div>
                                @error('origin')
                                    <p class="r-error">{{ $message }}</p>
                                @enderror
                            </div>

                            @if ($this->isExternal())
                                @include('partials.retina.field', ['name' => 'institution', 'label' => 'Universitas', 'autocomplete' => 'organization', 'placeholder' => 'Nama universitas', 'full' => true, 'maxlength' => 255])
                            @endif

                            @include('partials.retina.field', ['name' => 'faculty', 'label' => 'Fakultas', 'placeholder' => 'Contoh: Kedokteran', 'maxlength' => 255])
                            @include('partials.retina.field', ['name' => 'student_number', 'label' => $this->isExternal() ? 'NIM/NRP' : 'NRP', 'maxlength' => 50])
                            @include('partials.retina.field', ['name' => 'cohort_year', 'label' => 'Angkatan', 'inputmode' => 'numeric', 'placeholder' => 'Contoh: '.(now()->year - 1), 'maxlength' => 4])
                        </div>
                    </fieldset>

                    <fieldset class="r-fieldset">
                        <legend><span class="r-mono">03</span><span class="r-display">Pembayaran</span></legend>
                        <p class="r-lead" style="margin-top: 12px; font-size: 15px">Transfer <strong style="color: var(--ink)">{{ $fee }}</strong> ke rekening berikut, lalu unggah foto atau tangkapan layar bukti transfernya.</p>

                        <div style="margin-top: 20px">
                            @include('partials.retina.bank', ['compact' => true])
                        </div>

                        <div
                            class="r-field"
                            style="margin-top: 20px"
                            x-data="{ uploading: false, progress: 0 }"
                            x-on:livewire-upload-start="uploading = true"
                            x-on:livewire-upload-finish="uploading = false; progress = 0"
                            x-on:livewire-upload-cancel="uploading = false"
                            x-on:livewire-upload-error="uploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                        >
                            <span class="r-label" id="payment_proof-label">Bukti transfer</span>
                            <label class="r-drop" @error('payment_proof') aria-invalid="true" @enderror>
                                <input
                                    id="payment_proof"
                                    type="file"
                                    wire:model="payment_proof"
                                    accept="image/jpeg,image/png,image/webp"
                                    aria-labelledby="payment_proof-label"
                                    aria-describedby="payment_proof-hint @error('payment_proof') payment_proof-error @enderror"
                                >
                                <span class="r-drop__icon" aria-hidden="true">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg>
                                </span>
                                <span class="r-drop__text">
                                    <strong>{{ $payment_proof ? 'Ganti file' : 'Pilih foto bukti transfer' }}</strong>
                                    <span id="payment_proof-hint">JPG, PNG, atau WEBP · maksimal 4 MB</span>
                                </span>
                            </label>

                            <div class="r-progress" x-show="uploading" x-cloak role="progressbar" aria-label="Mengunggah bukti transfer" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100">
                                <span :style="`width: ${progress}%`"></span>
                            </div>

                            @error('payment_proof')
                                <p id="payment_proof-error" class="r-error">{{ $message }}</p>
                            @else
                                @if ($payment_proof && $payment_proof->isPreviewable())
                                    <div class="r-preview r-pop" wire:key="proof-{{ $payment_proof->getFilename() }}">
                                        <img src="{{ $payment_proof->temporaryUrl() }}" alt="Pratinjau bukti transfer">
                                        <div>
                                            <strong>{{ $payment_proof->getClientOriginalName() }}</strong>
                                            <span>Siap dikirim</span>
                                        </div>
                                    </div>
                                @endif
                            @enderror
                        </div>
                    </fieldset>

                    <div class="r-submit">
                        <p>Dengan mendaftar, kamu menyatakan data yang diisi sudah benar. Pendaftaran yang ditolak harus diulang dengan email lain.</p>
                        <button type="submit" class="r-btn r-btn--brick r-btn--lg" wire:loading.attr="disabled" wire:target="register,payment_proof">
                            <span wire:loading.remove wire:target="register">Kirim pendaftaran <span class="r-arrow" aria-hidden="true">→</span></span>
                            <span wire:loading.flex wire:target="register" style="align-items: center; gap: 10px"><span class="r-spinner" aria-hidden="true"></span> Mengirim…</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>

        <aside class="r-aside" aria-label="Ringkasan acara">
            @include('partials.retina.ticket', ['startsAt' => $startsAt])

            <div class="r-card r-aside__steps">
                <p class="r-ticket__label">Setelah kamu mengirim</p>
                <ol>
                    <li>Panitia memeriksa bukti transfermu.</li>
                    <li>Hasil verifikasi dikirim ke email.</li>
                    <li>Tunjukkan tiket QR saat registrasi ulang di {{ config('retina.venue.room') }}.</li>
                </ol>
                @if ($isRegistrationOpen && ($remainingQuota !== null || $registrationClosesAt))
                    <p class="r-hint" style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--line)">
                        @if ($remainingQuota !== null)
                            Sisa <strong style="color: var(--ink)">{{ $remainingQuota }} kursi</strong>.
                        @endif
                        @if ($registrationClosesAt)
                            Ditutup {{ $registrationClosesAt->locale('id')->translatedFormat('j F Y, H.i') }} WIB.
                        @endif
                    </p>
                @endif
            </div>
        </aside>
    </main>
</div>
