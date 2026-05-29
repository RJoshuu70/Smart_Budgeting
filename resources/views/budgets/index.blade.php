@extends('layouts.app')

@section('title', 'Anggaran - Smart Budgeting')

@section('content')

<div class="flex justify-between items-center mb-2">
    <h1 class="text-2xl font-bold text-gray-800">🎯 Anggaran</h1>
    <button onclick="document.getElementById('modal-add-budget').classList.remove('hidden')"
        class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-xl hover:bg-indigo-700">
        + Set Budget
    </button>
</div>
<p class="text-sm text-gray-500 mb-6">
    Minggu ini: {{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}
</p>

{{-- Daftar Budget --}}
@if($budgetData->count() > 0)
    <div class="space-y-4 mb-6">
        @foreach($budgetData as $item)
        @php
            $pct      = $item['percentage'];
            $barColor = match(true) {
                $pct >= 100 => 'bg-red-500',
                $pct >= 75  => 'bg-yellow-400',
                default     => 'bg-green-500',
            };
            $statusText = match(true) {
                $pct >= 100 => '⚠️ Over Budget!',
                $pct >= 75  => '⚠️ Hampir habis',
                default     => 'Aman',
            };
            $statusColor = match(true) {
                $pct >= 100 => 'text-red-500',
                $pct >= 75  => 'text-yellow-500',
                default     => 'text-green-500',
            };
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex justify-between items-center mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">{{ $item['budget']->category->icon ?? '🎯' }}</span>
                    <span class="font-semibold text-gray-700">
                        {{ $item['budget']->category->name ?? 'Total Budget' }}
                    </span>
                </div>
                <span class="{{ $statusColor }} text-xs font-semibold">{{ $statusText }}</span>
            </div>

            {{-- Progress Bar --}}
            <div class="w-full bg-gray-100 rounded-full h-2.5 mb-3">
                <div class="{{ $barColor }} h-2.5 rounded-full"
                     style="width: {{ min($pct, 100) }}%"></div>
            </div>

            <div class="flex justify-between text-xs text-gray-500">
                <span>Terpakai: <strong>Rp {{ number_format($item['spent'], 0, ',', '.') }}</strong></span>
                <span>Budget: <strong>Rp {{ number_format($item['budget']->amount, 0, ',', '.') }}</strong></span>
            </div>

            @if($item['remaining'] >= 0)
                <p class="text-xs text-gray-400 mt-1">
                    Sisa: Rp {{ number_format($item['remaining'], 0, ',', '.') }}
                    ({{ $pct }}% terpakai)
                </p>
            @else
                <p class="text-xs text-red-400 mt-1">
                    Melebihi budget: Rp {{ number_format(abs($item['remaining']), 0, ',', '.') }}
                </p>
            @endif
        </div>
        @endforeach
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center mb-6">
        <p class="text-4xl mb-3">🎯</p>
        <p class="text-gray-400 text-sm">Belum ada budget minggu ini</p>
        <p class="text-gray-300 text-xs mt-1">Tap "+ Set Budget" untuk mulai mengatur anggaran</p>
    </div>
@endif

{{-- Tips Card --}}
<div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4">
    <p class="text-sm font-semibold text-indigo-700 mb-1">💡 Tips Budget</p>
    <p class="text-xs text-indigo-500">
        Budget direset setiap Senin. Atur budget minggu ini sebelum mulai belanja
        agar pengeluaranmu lebih terkontrol.
    </p>
</div>

{{-- Modal Set Budget --}}
<div id="modal-add-budget" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end">
    <div class="bg-white w-full rounded-t-3xl p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800">Set Budget Minggu Ini</h2>
            <button onclick="document.getElementById('modal-add-budget').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>

        <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kategori <span class="text-gray-400 font-normal">(kosongkan untuk budget total)</span>
                </label>
                <select name="category_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">🎯 Total Budget (semua kategori)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Nominal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nominal Budget (Rp)
                </label>
                <input type="number" name="amount" placeholder="Contoh: 200000" min="1000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <p class="text-xs text-gray-400 mt-1">Budget berlaku untuk minggu ini (Senin–Minggu)</p>
            </div>

            @if($errors->any())
                <div class="p-3 bg-red-50 rounded-xl text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700">
                Simpan Budget
            </button>
        </form>
    </div>
</div>

@endsection