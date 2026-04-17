<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — {{ config('app.name', 'Gowolo') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f0fa 0%, #ede4f7 50%, #e8daf3 100%);
            color: #333;
            overflow: hidden;
        }

        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 560px;
            position: relative;
            z-index: 1;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #662c87, #8e44ad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.25rem;
            animation: fadeInDown 0.6s ease-out;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #4a1a6b;
            margin-bottom: 1rem;
            animation: fadeInDown 0.6s ease-out 0.15s both;
        }

        .error-message {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s ease-out 0.3s both;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.6s ease-out 0.45s both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #662c87, #8e44ad);
            color: #fff;
            box-shadow: 0 4px 15px rgba(102, 44, 135, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 44, 135, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: #662c87;
            border: 2px solid #662c87;
        }
        .btn-outline:hover {
            background: #662c87;
            color: #fff;
            transform: translateY(-2px);
        }

        .illustration {
            width: 200px;
            height: 200px;
            margin: 0 auto 1.5rem;
            position: relative;
            animation: float 3s ease-in-out infinite;
        }

        .illustration svg {
            width: 100%;
            height: 100%;
        }

        /* Floating circles background */
        .bg-circles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .bg-circles span {
            position: absolute;
            border-radius: 50%;
            background: rgba(102, 44, 135, 0.05);
        }
        .bg-circles span:nth-child(1) { width: 300px; height: 300px; top: -50px; right: -80px; }
        .bg-circles span:nth-child(2) { width: 200px; height: 200px; bottom: -40px; left: -60px; }
        .bg-circles span:nth-child(3) { width: 150px; height: 150px; top: 40%; left: 10%; background: rgba(142, 68, 173, 0.04); }
        .bg-circles span:nth-child(4) { width: 100px; height: 100px; bottom: 20%; right: 15%; background: rgba(102, 44, 135, 0.06); }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-10px); }
        }

        @media (max-width: 480px) {
            .error-code { font-size: 5rem; }
            .error-title { font-size: 1.2rem; }
            .illustration { width: 150px; height: 150px; }
        }
    </style>
</head>
<body>
    <div class="bg-circles">
        <span></span><span></span><span></span><span></span>
    </div>

    <div class="error-container">
        <div class="illustration">
            @yield('illustration')
        </div>

        <div class="error-code">@yield('code')</div>
        <h1 class="error-title">@yield('title')</h1>
        <p class="error-message">@yield('message')</p>

        <div class="error-actions">
            @yield('actions',
                '<a href="' . url('/') . '" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                    Back to Home
                </a>
                <a href="javascript:history.back()" class="btn btn-outline">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Go Back
                </a>'
            )
        </div>
    </div>
</body>
</html>
