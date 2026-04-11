<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; text-decoration: none!important; }
        a:hover { text-decoration: none; }
        .text-purple { color: rgba(104, 34, 139, 1); }

        body {
            font-family: 'Inter', sans-serif;
            background: url('{{ asset('newscreen/bg.webp') }}') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            padding: 20px;
        }

        .registration-container {
            background: rgba(228, 235, 245, 1);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 500px;
            margin: 2rem auto;
            backdrop-filter: blur(10px);
        }

        .form-section { padding: 10px 30px; }

        .section-title { text-align: center; }
        .section-subtitle { color: #6B7280; font-size: 16px; text-align: center; }
        .main-title { font-size: 32px; font-weight: 700; color: #1F2937; margin-bottom: 15px; }

        .form-group {
            margin-bottom: 15px;
            background: #F9FAFB;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            padding: 10px 12px;
            transition: all 0.3s ease;
        }
        .form-group:focus-within {
            border-color: rgba(104, 34, 139, 1);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            background: white;
        }

        .form-control {
            border: none !important;
            background: transparent;
            padding: 0;
            font-size: 16px;
            width: 100%;
            outline: none !important;
            box-shadow: none !important;
        }
        .form-control:focus {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent;
        }
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0px;
            display: block;
            background: transparent;
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            font-size: 18px;
        }
        .password-toggle:hover { color: rgba(104, 34, 139, 1); }

        .btn-primary {
            background: rgba(104, 34, 139, 1);
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
            background: linear-gradient(135deg, #7C3AED, #9333EA);
        }

        @media (max-width: 768px) {
            .registration-container { margin: 10px; border-radius: 16px; margin-top: 5rem; }
            .main-title { font-size: 28px; }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="form-section pt-4">
            <div class="section-title">
                Welcome back!
                <h1 class="main-title">Log In</h1>
            </div>

            @if(session('error'))
                <div class="alert alert-danger p-3 rounded mb-4">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success p-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger p-3 rounded mb-4">
                    <strong>Whoops! There were some problems with your input:</strong>
                    <div class="mt-2 mb-0">
                        @foreach ($errors->all() as $error)
                            <div>{!! $error !!}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('user.login.post') }}" method="POST" autocomplete="off">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="example@email.com"
                           value="{{ old('email') }}" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="position-relative">
                        <input type="password" class="form-control" name="password" id="password"
                               placeholder="••••••••" required autocomplete="off">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <div class="mt-5 text-center">
                <p class="my-0">Powered By</p>
                <img class="my-0" src="{{ asset('newscreen/gowolo-logo.webp') }}" alt="GoWolo" style="margin-top:-1rem!important" width="100">
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-group').forEach(function(group, index) {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
                setTimeout(function() {
                    group.style.transition = 'all 0.5s ease';
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
