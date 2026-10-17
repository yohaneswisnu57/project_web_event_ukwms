<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
@php
    $seoTitle = $title ?? config('retina.name').' — '.config('retina.tagline');
    $seoDescription = $description ?? config('retina.theme').'. '.\Illuminate\Support\Carbon::parse(config('retina.starts_at'))->locale('id')->translatedFormat('j F Y').' di '.config('retina.venue.room').', '.config('retina.venue.campus').'.';
    $seoImage = asset(config('retina.banner.file'));
@endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#3762AC">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('retina.name') }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @stack('head')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@900&family=Red+Hat+Text:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/retina.css') }}?v={{ filemtime(public_path('css/retina.css')) }}">
</head>
<body>
    <a class="r-skip" href="#konten">Langsung ke konten</a>

    @isset($slot)
        {{ $slot }}
    @else
        @yield('content')
    @endisset

    @include('partials.retina.footer')

    <script src="{{ asset('js/retina.js') }}?v={{ filemtime(public_path('js/retina.js')) }}" defer></script>
</body>
</html>
