@php
    $hasError = $errors->has($name);
    $describedBy = collect([
        filled($hint ?? null) ? "{$name}-hint" : null,
        $hasError ? "{$name}-error" : null,
    ])->filter()->implode(' ');
@endphp

<div class="r-field {{ ($full ?? false) ? 'r-col-full' : '' }}" wire:key="field-{{ $name }}">
    <label class="r-label" for="{{ $name }}">
        {{ $label }}
        @if ($optional ?? false)
            <span class="r-optional">(opsional)</span>
        @endif
    </label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type ?? 'text' }}"
        class="r-input"
        wire:model.blur="{{ $name }}"
        @if (filled($placeholder ?? null)) placeholder="{{ $placeholder }}" @endif
        @if (filled($autocomplete ?? null)) autocomplete="{{ $autocomplete }}" @endif
        @if (filled($inputmode ?? null)) inputmode="{{ $inputmode }}" @endif
        @if (filled($maxlength ?? null)) maxlength="{{ $maxlength }}" @endif
        @unless ($optional ?? false) aria-required="true" @endunless
        @if ($hasError) aria-invalid="true" @endif
        @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
    >
    @if (filled($hint ?? null))
        <p id="{{ $name }}-hint" class="r-hint">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="r-error">{{ $message }}</p>
    @enderror
</div>
