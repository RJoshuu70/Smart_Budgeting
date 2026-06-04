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

@section('title', 'Transaksi - Smart Budgeting')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="bi bi-wallet2 text-indigo-600 mr-1"></i> Transaksi
    </h1>
    <button onclick="document.getElementById('modal-add').classList.remove('hidden')"
        class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-xl hover:bg-indigo-700">
        + Tambah
    </button>
</div>

{{-- Daftar Transaksi --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    @if($transactions->count() > 0)
        <div class="divide-y divide-gray-50">
            @foreach($transactions as $trx)
            <div class="flex items-center justify-between p-4 hover:bg-gray-50">
                <div class="flex items-center gap-3">
                    <span class="text-xl"><i class="bi {{ $iconMap[$trx->category->name] ?? 'bi-cash-coin' }} text-indigo-500"></i></span>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $trx->category->name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $trx->date->translatedFormat('d M Y') }}
                            @if($trx->note)
                                · {{ $trx->note }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold {{ $trx->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $trx->type === 'income' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </span>
                    {{-- Tombol Hapus --}}
                    <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                          onsubmit="return confirm('Hapus transaksi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i class="bi bi-inbox text-4xl text-gray-300 mb-3 block"></i>
            <p class="text-gray-400 text-sm">Belum ada transaksi</p>
            <p class="text-gray-300 text-xs mt-1">Tap tombol + Tambah untuk mulai mencatat</p>
        </div>
    @endif
</div>

{{-- Modal Tambah Transaksi --}}
<div id="modal-add" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end">
    <div class="bg-white w-full rounded-t-3xl p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800">Tambah Transaksi</h2>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>

        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Tipe --}}
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center justify-center gap-2 border-2 rounded-xl p-3 cursor-pointer
                              has-[:checked]:border-red-400 has-[:checked]:bg-red-50">
                    <input type="radio" name="type" value="expense" checked class="hidden">
                    <span><i class="bi bi-dash-circle mr-1"></i> Pengeluaran</span>
                </label>
                <label class="flex items-center justify-center gap-2 border-2 rounded-xl p-3 cursor-pointer
                              has-[:checked]:border-green-400 has-[:checked]:bg-green-50">
                    <input type="radio" name="type" value="income" class="hidden">
                    <span><i class="bi bi-plus-circle mr-1"></i> Pemasukan</span>
                </label>
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Nominal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                <input type="number" name="amount" placeholder="0" min="1"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                <input type="text" name="note" placeholder="Contoh: makan siang bareng teman"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700">
                Simpan Transaksi
            </button>
        </form>

        @if($errors->any())
            <div class="mt-3 p-3 bg-red-50 rounded-xl text-sm text-red-600">
                {{ $errors->first() }}
            </div>
        @endif
    </div>
</div>

@endsection