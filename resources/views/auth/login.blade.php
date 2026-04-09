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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #662c87 0%, #1C2B36 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: rgba(228, 235, 245, 1);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 460px;
            overflow: hidden;
        }
        .login-header {
            background: #662c87;
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .login-header h4 { font-weight: 700; margin: 0; font-size: 1.3rem; }
        .login-header p  { margin: 5px 0 0; opacity: 0.8; font-size: 0.9rem; }
        .login-body { padding: 30px; }
        .form-control:focus { border-color: #662c87; box-shadow: 0 0 0 0.2rem rgba(102,44,135,0.25); }
        .btn-login {
            background: #662c87; color: #fff; border: none;
            width: 100%; padding: 12px; border-radius: 8px;
            font-weight: 600; font-size: 1rem;
        }
        .btn-login:hover { background: #7d38a6; color: #fff; }
        .input-group-text { background: #662c87; color: #fff; border-color: #662c87; }
        .portal-badge {
            display: inline-block; background: rgba(255,255,255,0.2);
            border-radius: 20px; padding: 4px 14px;
            font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <span class="portal-badge">Staff Portal</span>
        <h4><i class="fas fa-paint-brush mr-2"></i>Customization Portal</h4>
        <p>Sign in to manage customization requests</p>
    </div>
    <div class="login-body">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('portal.login.post') }}">
            @csrf
            <div class="form-group">
                <label><i class="fas fa-envelope mr-1"></i> Email Address</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           placeholder="admin@gowologlobal.com" value="{{ old('email') }}" autofocus required>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-lock mr-1"></i> Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt mr-2"></i> Sign In
            </button>
        </form>

        <div class="text-center mt-3" style="font-size:0.8rem; color:#888;">
            Powered by <strong>GoWolo</strong>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var input = document.getElementById('passwordInput');
        var icon  = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>
</body>
</html>
