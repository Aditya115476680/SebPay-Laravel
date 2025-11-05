<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kasir | SebPay</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
        <!-- Lingkaran Dekorasi -->
        <div class="circle circle-top-right"></div>
        <div class="circle circle-bottom-left"></div>

    <div class="login-container">
        <div class="logo">
            <img src="{{ asset('images/sebpay_logo.png') }}" alt="SebPay Logo">
        </div>

        <div class="login-box">
            <h2>Login Kasir</h2>
            <form action="{{ route('login.process') }}" method="POST">
                @csrf
            
                <div class="input-group">
                    <input type="text" name="username" placeholder="username" required>
                </div>
            
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
            
                <button type="submit">Login</button>
            
                @if(session('error'))
                    <p style="color: red;">{{ session('error') }}</p>
                @endif
            </form>
            
            
        </div>

        <footer>
            © 2025 Kasir SebPay — Muhammad Aditya MayLingga
        </footer>
    </div>

</body>
</html>
