<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Budgeting')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">
                💰 Smart Budgeting
            </a>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">Hai, {{ Auth::user()->name }} 👋</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="text-sm text-red-500 hover:text-red-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Bottom Navigation (mobile-friendly) --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
        <div class="flex justify-around items-center py-2">
            <a href="{{ route('dashboard') }}" 
               class="flex flex-col items-center text-xs {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-500' }}">
                🏠 <span>Beranda</span>
            </a>
            <a href="{{ route('transactions.index') }}" 
               class="flex flex-col items-center text-xs {{ request()->routeIs('transactions*') ? 'text-indigo-600' : 'text-gray-500' }}">
                📝 <span>Transaksi</span>
            </a>
            <a href="{{ route('budgets.index') }}" 
               class="flex flex-col items-center text-xs {{ request()->routeIs('budgets*') ? 'text-indigo-600' : 'text-gray-500' }}">
                🎯 <span>Anggaran</span>
            </a>
            <a href="{{ route('reports.index') }}" 
               class="flex flex-col items-center text-xs {{ request()->routeIs('reports*') ? 'text-indigo-600' : 'text-gray-500' }}">
                📊 <span>Laporan</span>
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