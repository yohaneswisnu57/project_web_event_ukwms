<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('retina.name').' — '.config('retina.tagline') }}</title>
    <meta name="description" content="{{ config('retina.theme') }} — {{ config('retina.venue.room') }}, {{ config('retina.venue.campus') }}.">
    <meta name="theme-color" content="#0B1F4B">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ config('retina.name') }} — {{ config('retina.tagline') }}">
    <meta property="og:description" content="{{ config('retina.theme') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

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
