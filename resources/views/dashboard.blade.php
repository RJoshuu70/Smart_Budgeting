@php
$iconMap = [
    'Makan & Minum'  => 'bi-cup-hot-fill',
    'Transport'      => 'bi-bus-front-fill',
    'Akademik'       => 'bi-book-fill',
    'Hiburan'        => 'bi-controller',
    'Blind Box'      => 'bi-gift-fill',
    'Ngopi'          => 'bi-cup-fill',
    'Kesehatan'      => 'bi-heart-pulse-fill',
    'Kos/Kontrakan'  => 'bi-house-fill',
    'Kuota/Internet' => 'bi-wifi',
    'Lainnya'        => 'bi-three-dots',
];
@endphp

@extends('layouts.app')

@section('title', 'Dashboard - Smart Budgeting')

@section('content')

{{-- Header Greeting --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Hai, {{ Auth::user()->name }}
    </h1>
    <p class="text-sm text-gray-500 mt-1">
        Minggu ini: {{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}
    </p>
</div>

{{-- Card Pemasukan & Pengeluaran --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
        <p class="text-xs text-green-600 font-medium mb-1">Pemasukan Minggu Ini</p>
        <p class="text-lg font-bold text-green-700">
            Rp {{ number_format($weeklyIncome, 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <p class="text-xs text-red-600 font-medium mb-1">Pengeluaran Minggu Ini</p>
        <p class="text-lg font-bold text-red-700">
            Rp {{ number_format($weeklyExpense, 0, ',', '.') }}
        </p>
    </div>
</div>

{{-- Budget Status Card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <div class="flex justify-between items-center mb-3">
        <h2 class="font-semibold text-gray-700">
            <i class="bi bi-bullseye mr-1"></i> Budget Minggu Ini
        </h2>
        <a href="{{ route('budgets.index') }}" class="text-xs text-indigo-500 hover:underline">Atur</a>
    </div>

    @if($budgetStatus['has_budget'])
        @php
            $pct    = $budgetStatus['percentage'];
            $status = $budgetStatus['status'];
            $barColor = match($status) {
                'over'    => 'bg-red-500',
                'warning' => 'bg-yellow-400',
                default   => 'bg-green-500',
            };
            $textColor = match($status) {
                'over'    => 'text-red-600',
                'warning' => 'text-yellow-600',
                default   => 'text-green-600',
            };
        @endphp

        <div class="flex justify-between text-sm mb-2">
            <span class="text-gray-600">
                Terpakai: <strong>Rp {{ number_format($budgetStatus['spent'], 0, ',', '.') }}</strong>
            </span>
            <span class="{{ $textColor }} font-semibold">{{ $pct }}%</span>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 rounded-full h-3 mb-2">
            <div class="{{ $barColor }} h-3 rounded-full transition-all"
                 style="width: {{ min($pct, 100) }}%"></div>
        </div>

        <div class="flex justify-between text-xs text-gray-500">
            <span>Budget: Rp {{ number_format($budgetStatus['budget'], 0, ',', '.') }}</span>
            @if($status === 'over')
                <span class="text-red-500 font-semibold">
                    <i class="bi bi-exclamation-triangle-fill mr-1"></i> Over Budget!
                </span>
            @elseif($status === 'warning')
                <span class="text-yellow-500 font-semibold">
                    <i class="bi bi-exclamation-triangle-fill mr-1"></i> Hampir habis
                </span>
            @else
                <span class="text-green-500">Sisa: Rp {{ number_format($budgetStatus['remaining'], 0, ',', '.') }}</span>
            @endif
        </div>

    @else
        <div class="text-center py-4">
            <p class="text-gray-400 text-sm mb-3">Budget minggu ini belum diatur</p>
            <a href="{{ route('budgets.index') }}"
               class="inline-block bg-indigo-600 text-white text-sm px-4 py-2 rounded-xl hover:bg-indigo-700">
                + Set Budget
            </a>
        </div>
    @endif
</div>

{{-- Top Pengeluaran --}}
@if($topCategories->count() > 0)
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <h2 class="font-semibold text-gray-700 mb-4">
        <i class="bi bi-fire mr-1"></i> Pengeluaran Terbesar Minggu Ini
    </h2>
    <div class="space-y-3">
        @foreach($topCategories as $item)
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl"><i class="bi {{ $iconMap[$item->category->name] ?? 'bi-cash-coin' }} text-indigo-500 text-xl"></i></span>
                <span class="text-sm text-gray-700">{{ $item->category->name ?? 'Lainnya' }}</span>
            </div>
            <span class="text-sm font-semibold text-red-600">
                Rp {{ number_format($item->total, 0, ',', '.') }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Transaksi Terbaru --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-gray-700">
            <i class="bi bi-clock-history mr-1"></i> Transaksi Terbaru
        </h2>
        <a href="{{ route('transactions.index') }}" class="text-xs text-indigo-500 hover:underline">Lihat semua</a>
    </div>

    @if($recentTransactions->count() > 0)
        <div class="space-y-3">
            @foreach($recentTransactions as $trx)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl"><i class="bi {{ $iconMap[$trx->category->name] ?? 'bi-cash-coin' }} text-indigo-500"></i></span>
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $trx->category->name ?? 'Lainnya' }}</p>
                        <p class="text-xs text-gray-400">{{ $trx->date->translatedFormat('d M Y') }}</p>
                    </div>
                </div>
                <span class="text-sm font-semibold {{ $trx->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $trx->type === 'income' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                </span>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-6">
            <p class="text-gray-400 text-sm">Belum ada transaksi</p>
            <a href="{{ route('transactions.index') }}"
               class="inline-block mt-3 bg-indigo-600 text-white text-sm px-4 py-2 rounded-xl hover:bg-indigo-700">
                + Catat Transaksi
            </a>
        </div>
    @endif
</div>

@endsection