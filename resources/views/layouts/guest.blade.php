<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Event - UKWMS</title>
    <meta name="description" content="Form pendaftaran kehadiran event UKWMS">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    @livewireStyles
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f0c29;
        }
        ::-webkit-scrollbar-thumb {
            background: #302b63;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #f59e0b;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-4 text-slate-200" 
      style="background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);">
    
    <div class="w-full max-w-lg">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
