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
                    <span class="text-xl">
                        <i class="bi {{ $iconMap[$trx->category->name] ?? 'bi-cash-coin' }} text-indigo-500"></i>
                    </span>
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
                    <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                          onsubmit="return confirm('Hapus transaksi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs">&times;</button>
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

@endsection

@push('scripts')

{{-- Modal Tambah Transaksi --}}
<div id="modal-add"
     class="hidden fixed inset-0 z-[999]"
     style="background: rgba(0,0,0,0.5);"
     onclick="if(event.target===this)this.classList.add('hidden')">

    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800">Tambah Transaksi</h2>
            <button type="button"
                onclick="document.getElementById('modal-add').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
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

            {{-- Kategori dengan Custom Dropdown Bootstrap Icons --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>

                <input type="hidden" name="category_id" id="trx_selected_category_id"
                       value="{{ $categories->first()->id ?? '' }}">

                <div class="relative">
                    <button type="button" id="trx-category-trigger"
                        onclick="toggleTrxDropdown()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm text-left flex items-center gap-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @if($categories->first())
                        <i class="bi {{ $iconMap[$categories->first()->name] ?? 'bi-cash-coin' }} text-indigo-500"
                           id="trx-selected-icon"></i>
                        <span id="trx-selected-label" class="text-gray-800 flex-1">
                            {{ $categories->first()->name }}
                        </span>
                        @else
                        <i class="bi bi-cash-coin text-gray-400" id="trx-selected-icon"></i>
                        <span id="trx-selected-label" class="text-gray-400 flex-1">Pilih kategori</span>
                        @endif
                        <i class="bi bi-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <div id="trx-category-dropdown"
                         class="hidden absolute bottom-full left-0 right-0 mb-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto z-10">
                        @foreach($categories->unique('name') as $cat)
                        @php $catIcon = $iconMap[$cat->name] ?? 'bi-cash-coin'; @endphp
                        <button type="button"
                            onclick="selectTrxCategory('{{ $cat->id }}', '{{ $catIcon }}', '{{ $cat->name }}')"
                            class="w-full px-4 py-3 text-sm text-left flex items-center gap-3 hover:bg-indigo-50 border-b border-gray-50 last:border-0">
                            <i class="bi {{ $catIcon }} text-indigo-500"></i>
                            <span class="text-gray-700">{{ $cat->name }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
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

            @if($errors->any())
                <div class="p-3 bg-red-50 rounded-xl text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700">
                Simpan Transaksi
            </button>
        </form>
    </div>
</div>

<script>
function toggleTrxDropdown() {
    document.getElementById('trx-category-dropdown').classList.toggle('hidden');
}

function selectTrxCategory(id, icon, label) {
    document.getElementById('trx_selected_category_id').value = id;
    document.getElementById('trx-selected-icon').className = 'bi ' + icon + ' text-indigo-500';
    document.getElementById('trx-selected-label').textContent = label;
    document.getElementById('trx-selected-label').className = 'text-gray-800 flex-1';
    document.getElementById('trx-category-dropdown').classList.add('hidden');
}

document.addEventListener('click', function(e) {
    const trigger  = document.getElementById('trx-category-trigger');
    const dropdown = document.getElementById('trx-category-dropdown');
    if (trigger && dropdown && !trigger.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>

@endpush