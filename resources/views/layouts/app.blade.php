@php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    $cssFile = $manifest['resources/css/app.css']['file'] ?? 'assets/app.css';
    $jsFile = $manifest['resources/js/app.js']['file'] ?? 'assets/app.js';
@endphp
<link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
<script type="module" src="{{ asset('build/' . $jsFile) }}" defer></script>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Budgeting')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('build/assets/app-DpYi8pJZ.css') }}">
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-xl font-bold text-indigo-600">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8">
                <span>Smart Budgeting</span>
            </a>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">Hai, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Bottom Navigation --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
        <div class="flex justify-around items-center py-2">
            <a href="{{ route('dashboard') }}"
            class="flex flex-col items-center text-xs gap-1 px-3 py-1
                    {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400' }}">
                <i class="bi bi-house-door text-xl"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('transactions.index') }}"
            class="flex flex-col items-center text-xs gap-1 px-3 py-1
                    {{ request()->routeIs('transactions*') ? 'text-indigo-600' : 'text-gray-400' }}">
                <i class="bi bi-wallet2 text-xl"></i>
                <span>Transaksi</span>
            </a>
            <a href="{{ route('budgets.index') }}"
            class="flex flex-col items-center text-xs gap-1 px-3 py-1
                    {{ request()->routeIs('budgets*') ? 'text-indigo-600' : 'text-gray-400' }}">
                <i class="bi bi-bullseye text-xl"></i>
                <span>Anggaran</span>
            </a>
            <a href="{{ route('reports.index') }}"
            class="flex flex-col items-center text-xs gap-1 px-3 py-1
                    {{ request()->routeIs('reports*') ? 'text-indigo-600' : 'text-gray-400' }}">
                <i class="bi bi-file-earmark-bar-graph text-xl"></i>
                <span>Laporan</span>
            </a>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-6xl mx-auto px-4 py-6 pb-24">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>