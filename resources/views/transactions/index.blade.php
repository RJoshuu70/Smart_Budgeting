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
    <button onclick="openAddModal()"
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
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <span class="text-xl flex-shrink-0">
                        <i class="bi {{ $iconMap[$trx->category->name] ?? 'bi-cash-coin' }} text-indigo-500"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800">{{ $trx->category->name }}</p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ $trx->date->translatedFormat('d M Y') }}
                            @if($trx->note) · {{ $trx->note }} @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                    <span class="text-sm font-bold {{ $trx->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $trx->type === 'income' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </span>
                    {{-- Tombol Edit --}}
                    <button type="button"
                        onclick="openEditModal(
                            {{ $trx->id }},
                            '{{ $trx->type }}',
                            {{ $trx->category_id }},
                            '{{ $iconMap[$trx->category->name] ?? 'bi-cash-coin' }}',
                            '{{ $trx->category->name }}',
                            {{ $trx->amount }},
                            '{{ $trx->date->format('Y-m-d') }}',
                            '{{ addslashes($trx->note ?? '') }}'
                        )"
                        class="text-indigo-400 hover:text-indigo-600 text-sm p-1">
                        <i class="bi bi-pencil"></i>
                    </button>
                    {{-- Tombol Hapus --}}
                    <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                          onsubmit="return confirm('Hapus transaksi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 text-sm p-1">
                            <i class="bi bi-trash"></i>
                        </button>
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
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 overflow-y-auto"
         style="max-height: 92vh;">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800">Tambah Transaksi</h2>
            <button type="button" onclick="this.closest('#modal-add, [id=modal-add]').classList.add('hidden')"
                    onclick="document.getElementById('modal-add').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
            @csrf
            {{-- Tipe --}}
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center justify-center gap-2 border-2 rounded-xl p-3 cursor-pointer has-[:checked]:border-red-400 has-[:checked]:bg-red-50">
                    <input type="radio" name="type" value="expense" checked class="hidden">
                    <span><i class="bi bi-dash-circle mr-1"></i> Pengeluaran</span>
                </label>
                <label class="flex items-center justify-center gap-2 border-2 rounded-xl p-3 cursor-pointer has-[:checked]:border-green-400 has-[:checked]:bg-green-50">
                    <input type="radio" name="type" value="income" class="hidden">
                    <span><i class="bi bi-plus-circle mr-1"></i> Pemasukan</span>
                </label>
            </div>
            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <input type="hidden" name="category_id" id="add_category_id" value="{{ $categories->first()->id ?? '' }}">
                <button type="button" id="add-cat-trigger" onclick="toggleDropdown('add-cat-dropdown')"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm text-left flex items-center gap-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <i class="bi {{ $iconMap[$categories->first()->name ?? ''] ?? 'bi-cash-coin' }} text-indigo-500" id="add-cat-icon"></i>
                    <span id="add-cat-label" class="text-gray-800 flex-1">{{ $categories->first()->name ?? 'Pilih kategori' }}</span>
                    <i class="bi bi-chevron-up text-gray-400 text-xs"></i>
                </button>
                <div id="add-cat-dropdown" class="hidden mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-44 overflow-y-auto z-10">
                    @foreach($categories->unique('name') as $cat)
                    @php $ci = $iconMap[$cat->name] ?? 'bi-cash-coin'; @endphp
                    <button type="button"
                        onclick="selectCat('add', '{{ $cat->id }}', '{{ $ci }}', '{{ $cat->name }}')"
                        class="w-full px-4 py-3 text-sm text-left flex items-center gap-3 hover:bg-indigo-50 border-b border-gray-50 last:border-0">
                        <i class="bi {{ $ci }} text-indigo-500"></i>
                        <span class="text-gray-700">{{ $cat->name }}</span>
                    </button>
                    @endforeach
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
                <div class="p-3 bg-red-50 rounded-xl text-sm text-red-600">{{ $errors->first() }}</div>
            @endif
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700">
                Simpan Transaksi
            </button>
        </form>
    </div>
</div>

{{-- Modal Edit Transaksi --}}
<div id="modal-edit"
     class="hidden fixed inset-0 z-[999]"
     style="background: rgba(0,0,0,0.5);"
     onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 overflow-y-auto"
         style="max-height: 92vh;">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800">Edit Transaksi</h2>
            <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form id="edit-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            {{-- Tipe --}}
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center justify-center gap-2 border-2 rounded-xl p-3 cursor-pointer has-[:checked]:border-red-400 has-[:checked]:bg-red-50">
                    <input type="radio" name="type" id="edit-type-expense" value="expense" class="hidden">
                    <span><i class="bi bi-dash-circle mr-1"></i> Pengeluaran</span>
                </label>
                <label class="flex items-center justify-center gap-2 border-2 rounded-xl p-3 cursor-pointer has-[:checked]:border-green-400 has-[:checked]:bg-green-50">
                    <input type="radio" name="type" id="edit-type-income" value="income" class="hidden">
                    <span><i class="bi bi-plus-circle mr-1"></i> Pemasukan</span>
                </label>
            </div>
            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <input type="hidden" name="category_id" id="edit_category_id">
                <button type="button" id="edit-cat-trigger" onclick="toggleDropdown('edit-cat-dropdown')"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm text-left flex items-center gap-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <i class="bi bi-cash-coin text-indigo-500" id="edit-cat-icon"></i>
                    <span id="edit-cat-label" class="text-gray-800 flex-1">Pilih kategori</span>
                    <i class="bi bi-chevron-up text-gray-400 text-xs"></i>
                </button>
                <div id="edit-cat-dropdown" class="hidden mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-44 overflow-y-auto z-10">
                    @foreach($categories->unique('name') as $cat)
                    @php $ci = $iconMap[$cat->name] ?? 'bi-cash-coin'; @endphp
                    <button type="button"
                        onclick="selectCat('edit', '{{ $cat->id }}', '{{ $ci }}', '{{ $cat->name }}')"
                        class="w-full px-4 py-3 text-sm text-left flex items-center gap-3 hover:bg-indigo-50 border-b border-gray-50 last:border-0">
                        <i class="bi {{ $ci }} text-indigo-500"></i>
                        <span class="text-gray-700">{{ $cat->name }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            {{-- Nominal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                <input type="number" name="amount" id="edit-amount" placeholder="0" min="1"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            {{-- Tanggal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" id="edit-date"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                <input type="text" name="note" id="edit-note" placeholder="Contoh: makan siang bareng teman"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<script>
// ── Dropdown ──────────────────────────────────────────────
function toggleDropdown(id) {
    document.getElementById(id).classList.toggle('hidden');
}

function selectCat(prefix, id, icon, label) {
    document.getElementById(prefix + '_category_id').value = id;
    document.getElementById(prefix + '-cat-icon').className = 'bi ' + icon + ' text-indigo-500';
    document.getElementById(prefix + '-cat-label').textContent = label;
    document.getElementById(prefix + '-cat-label').className  = 'text-gray-800 flex-1';
    document.getElementById(prefix + '-cat-dropdown').classList.add('hidden');
}

// Tutup dropdown kalau klik di luar
document.addEventListener('click', function(e) {
    ['add', 'edit'].forEach(function(prefix) {
        const trigger  = document.getElementById(prefix + '-cat-trigger');
        const dropdown = document.getElementById(prefix + '-cat-dropdown');
        if (trigger && dropdown && !trigger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});

// ── Modal Tambah ──────────────────────────────────────────
function openAddModal() {
    document.getElementById('modal-add').classList.remove('hidden');
}

// ── Modal Edit ────────────────────────────────────────────
function openEditModal(id, type, catId, catIcon, catName, amount, date, note) {
    // Set action form
    document.getElementById('edit-form').action = '/transactions/' + id;

    // Set tipe
    document.getElementById('edit-type-expense').checked = (type === 'expense');
    document.getElementById('edit-type-income').checked  = (type === 'income');

    // Set kategori
    document.getElementById('edit_category_id').value = catId;
    document.getElementById('edit-cat-icon').className = 'bi ' + catIcon + ' text-indigo-500';
    document.getElementById('edit-cat-label').textContent = catName;

    // Set nilai lainnya
    document.getElementById('edit-amount').value = amount;
    document.getElementById('edit-date').value   = date;
    document.getElementById('edit-note').value   = note;

    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>

@endpush