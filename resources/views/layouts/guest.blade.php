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
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            color: #e2e8f0;
        }

        .container {
            width: 100%;
            max-width: 520px;
        }

        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .card-header .badge {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #1a1a2e;
            padding: 0.35rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .card-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.3;
        }

        .card-header .company-name {
            font-size: 1rem;
            color: #94a3b8;
            margin-top: 0.5rem;
            font-weight: 400;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.15), transparent);
            margin: 1.5rem 0;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.85rem 1rem;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: #fff;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input::placeholder {
            color: #64748b;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
            background: rgba(255, 255, 255, 0.1);
        }

        .form-group input[type="number"] {
            -moz-appearance: textfield;
        }

        .form-group input[type="number"]::-webkit-inner-spin-button,
        .form-group input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
        }

        .error-message {
            color: #f87171;
            font-size: 0.78rem;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .error-message::before {
            content: '⚠';
            font-size: 0.7rem;
        }

        .form-group.has-error input {
            border-color: #f87171;
            box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #1a1a2e;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-submit .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(26, 26, 46, 0.3);
            border-top-color: #1a1a2e;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            vertical-align: middle;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Success State */
        .success-card {
            text-align: center;
            padding: 3rem 2rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: bounceIn 0.5s ease-out;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        .success-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }

        .success-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .success-card p {
            color: #94a3b8;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Already registered state */
        .already-registered {
            text-align: center;
            padding: 3rem 2rem;
        }

        .already-registered .info-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .already-registered .info-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }

        .already-registered h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .already-registered p {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .card {
                padding: 1.75rem;
                border-radius: 18px;
            }

            .card-header h1 {
                font-size: 1.4rem;
            }
        }
    </style>
    @livewireStyles
</head>
<body>
    <div class="container">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
