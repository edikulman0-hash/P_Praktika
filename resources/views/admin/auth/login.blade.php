<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход в админ-панель — KinoTower</title>

    <link rel="stylesheet" href="https://googleapis.com">
    <link rel="stylesheet" href="https://cloudflare.com">
    <link rel="stylesheet" href="https://jsdelivr.net">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>KinoTower</b> Admin
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Войдите для начала сеанса</p>

            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Пароль" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Войти</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://jsdelivr.net"></script>
<script src="https://jsdelivr.net"></script>
<script src="https://jsdelivr.net"></script>
</body>
</html>
