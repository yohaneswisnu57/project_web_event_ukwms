@php
    $accountNumber = (string) config('retina.bank.account_number');
    $groupedNumber = preg_replace('/^(\d{3})(\d{3})(\d{3})(\d+)$/', '$1 $2 $3 $4', $accountNumber);
@endphp

<div class="r-card r-bank {{ ($compact ?? false) ? 'r-bank--compact' : '' }}">
    <div>
        <p class="r-ticket__label">Rekening panitia · {{ config('retina.bank.name') }}</p>
        <p class="r-bank__number" aria-label="Nomor rekening {{ $accountNumber }}">{{ $groupedNumber }}</p>
        <p class="r-bank__holder">a.n. {{ config('retina.bank.account_holder') }}</p>
    </div>
    <button type="button" class="r-btn r-btn--outline r-copy" data-copy="{{ $accountNumber }}">
        <span data-copy-label>Salin nomor</span>
    </button>
</div>
