<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event UKWMS</title>
    <meta name="description" content="Official Event Page UKWMS">
    <link rel="icon" type="image/x-icon" href="https://www.ukwms.ac.id/favicon.ico">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --font-display: 'DM Serif Display', Georgia, serif;
            --color-paper: #f1f0ee;
            --color-ink: #121212;
            --color-wm-red: #a8191f;
            --color-wm-yellow: #FAC43A;
        }
    </style>
    @include('partials.landing.motion-head')
    @livewireStyles
    
    <style>
        [x-cloak] { display: none !important; }
        
        :root {
            --ukwms-yellow: #FAC43A;
            --ukwms-yellow-hover: #e5b334;
        }

        body {
            font-family: 'Outfit', sans-serif;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--ukwms-yellow);
        }
    </style>
</head>
<body class="antialiased min-h-screen text-ink bg-paper">
    
    <div class="w-full">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </div>

    @livewireScripts
    @include('partials.landing.motion-scripts')
</body>
</html>
