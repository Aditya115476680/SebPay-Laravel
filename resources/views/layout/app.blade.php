<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SebPay')</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kelola_topping.css') }}">
    <link rel="stylesheet" href="{{ asset('css/topping_in.css') }}">
    <link rel="stylesheet" href="{{ asset('css/topping_out.css') }}">
    <link rel="stylesheet" href="{{ asset('css/transaksi.css') }}">
    <link rel="stylesheet" href="{{ asset('css/history.css') }}">

</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/sebpay_logo.png') }}" alt="SebPay Logo">
            <h2>SebPay</h2>
        </div>
        <ul class="menu">
            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li class="{{ request()->is('toppings') ? 'active' : '' }}">
                <a href="{{ url('/toppings') }}">Kelola Topping</a>
            </li>
            <li class="{{ request()->is('topping-in') ? 'active' : '' }}">
                <a href="{{ url('/topping-in') }}">Topping In</a>
            </li>
            <li class="{{ request()->is('topping-out') ? 'active' : '' }}">
                <a href="{{ url('/topping-out') }}">Topping Out</a>
            </li>
            <li class="{{ request()->is('transactions') ? 'active' : '' }}">
                <a href="{{ url('/transactions') }}">Transaksi</a>
            </li>
            <a href="{{ route('transactions.history') }}">Riwayat Transaksi</a></li> {{-- ✅ Tambahkan ini --}}
            <li>
                <a href="{{ url('/logout') }}">Logout</a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

</body>
</html>
