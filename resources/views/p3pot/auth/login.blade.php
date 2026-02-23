<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P3POT - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="login-card shadow-lg">
        <h2 class="text-center mb-4">P3POT LOGIN</h2>
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('p3pot.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control bg-dark text-white border-secondary" autocomplete="username" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control bg-dark text-white border-secondary" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill">Sign In</button>
        </form>
    </div>
</body>
</html>
