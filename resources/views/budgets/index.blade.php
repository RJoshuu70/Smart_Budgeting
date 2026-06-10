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

@section('title', 'Anggaran - Smart Budgeting')

@section('content')

<div class="flex justify-between items-center mb-2">
    <h1 class="text-2xl font-bold text-gray-800">
        <i class="bi bi-bullseye text-indigo-600 mr-1"></i> Anggaran
    </h1>
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
            $catName  = $item['budget']->category->name ?? 'Total Budget';
            $catIcon  = $iconMap[$catName] ?? 'bi-bullseye';
            $barColor = match(true) {
                $pct >= 100 => 'bg-red-500',
                $pct >= 75  => 'bg-yellow-400',
                default     => 'bg-green-500',
            };
            $statusText = match(true) {
                $pct >= 100 => 'Over Budget!',
                $pct >= 75  => 'Hampir habis',
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
                    <i class="bi {{ $catIcon }} text-indigo-500 text-xl"></i>
                    <span class="font-semibold text-gray-700">{{ $catName }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="{{ $statusColor }} text-xs font-semibold">{{ $statusText }}</span>
                    {{-- Tombol Edit --}}
                    <button type="button"
                        onclick="openEditBudget({{ $item['budget']->id }}, {{ $item['budget']->amount }})"
                        class="text-indigo-400 hover:text-indigo-600 text-sm p-1">
                        <i class="bi bi-pencil"></i>
                    </button>
                    {{-- Tombol Hapus --}}
                    <form action="{{ route('budgets.destroy', $item['budget']->id) }}" method="POST"
                          onsubmit="return confirm('Hapus budget ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 text-sm p-1">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

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
        <i class="bi bi-bullseye text-4xl text-gray-300 mb-3 block"></i>
        <p class="text-gray-400 text-sm">Belum ada budget minggu ini</p>
        <p class="text-gray-300 text-xs mt-1">Tap "+ Set Budget" untuk mulai mengatur anggaran</p>
    </div>
@endif

{{-- Tips Card --}}
<div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 mb-6">
    <p class="text-sm font-semibold text-indigo-700 mb-1">
        <i class="bi bi-lightbulb-fill mr-1"></i> Tips Budget
    </p>
    <p class="text-xs text-indigo-500">
        Budget direset setiap Senin. Atur budget minggu ini sebelum mulai belanja
        agar pengeluaranmu lebih terkontrol.
    </p>
</div>

@endsection

@push('scripts')

{{-- Modal Set Budget --}}
<div id="modal-add-budget"
     class="hidden fixed inset-0 z-[999]"
     style="background: rgba(0,0,0,0.5);"
     onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 overflow-y-auto"
         style="max-height: 92vh;">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800">Set Budget Minggu Ini</h2>
            <button type="button"
                onclick="document.getElementById('modal-add-budget').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kategori <span class="text-gray-400 font-normal">(kosongkan untuk budget total)</span>
                </label>
                <input type="hidden" name="category_id" id="add_budget_cat_id" value="">
                <button type="button" id="add-budget-cat-trigger"
                    onclick="toggleDropdown('add-budget-cat-dropdown')"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm text-left flex items-center gap-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <i class="bi bi-bullseye text-gray-400" id="add-budget-cat-icon"></i>
                    <span id="add-budget-cat-label" class="text-gray-400 flex-1">Total Budget (semua kategori)</span>
                    <i class="bi bi-chevron-up text-gray-400 text-xs"></i>
                </button>
                <div id="add-budget-cat-dropdown"
                     class="hidden mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-44 overflow-y-auto z-10">
                    <button type="button"
                        onclick="selectBudgetCat('add', '', 'bi-bullseye', 'Total Budget (semua kategori)', false)"
                        class="w-full px-4 py-3 text-sm text-left flex items-center gap-3 hover:bg-indigo-50 border-b border-gray-50">
                        <i class="bi bi-bullseye text-gray-400"></i>
                        <span class="text-gray-500">Total Budget (semua kategori)</span>
                    </button>
                    @foreach($categories->unique('name') as $cat)
                    @php $ci = $iconMap[$cat->name] ?? 'bi-cash-coin'; @endphp
                    <button type="button"
                        onclick="selectBudgetCat('add', '{{ $cat->id }}', '{{ $ci }}', '{{ $cat->name }}', true)"
                        class="w-full px-4 py-3 text-sm text-left flex items-center gap-3 hover:bg-indigo-50 border-b border-gray-50 last:border-0">
                        <i class="bi {{ $ci }} text-indigo-500"></i>
                        <span class="text-gray-700">{{ $cat->name }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Budget (Rp)</label>
                <input type="number" name="amount" placeholder="Contoh: 200000" min="1000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <p class="text-xs text-gray-400 mt-1">Budget berlaku untuk minggu ini (Senin–Minggu)</p>
            </div>
            @if($errors->any())
                <div class="p-3 bg-red-50 rounded-xl text-sm text-red-600">{{ $errors->first() }}</div>
            @endif
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700">
                Simpan Budget
            </button>
        </form>
    </div>
</div>

{{-- Modal Edit Budget --}}
<div id="modal-edit-budget"
     class="hidden fixed inset-0 z-[999]"
     style="background: rgba(0,0,0,0.5);"
     onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 overflow-y-auto"
         style="max-height: 92vh;">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold text-gray-800">Edit Budget</h2>
            <button type="button"
                onclick="document.getElementById('modal-edit-budget').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form id="edit-budget-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Budget (Rp)</label>
                <input type="number" name="amount" id="edit-budget-amount" placeholder="Contoh: 200000" min="1000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <p class="text-xs text-gray-400 mt-1">Kategori tidak dapat diubah. Hapus dan buat baru jika ingin mengganti kategori.</p>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<script>
function toggleDropdown(id) {
    document.getElementById(id).classList.toggle('hidden');
}

function selectBudgetCat(prefix, id, icon, label, isCategory) {
    document.getElementById(prefix + '_budget_cat_id').value = id;
    const iconEl  = document.getElementById(prefix + '-budget-cat-icon');
    const labelEl = document.getElementById(prefix + '-budget-cat-label');
    iconEl.className  = 'bi ' + icon + (isCategory ? ' text-indigo-500' : ' text-gray-400');
    labelEl.textContent = label;
    labelEl.className = (isCategory ? 'text-gray-800' : 'text-gray-400') + ' flex-1';
    document.getElementById(prefix + '-budget-cat-dropdown').classList.add('hidden');
}

function openEditBudget(id, amount) {
    document.getElementById('edit-budget-form').action = '/budgets/' + id;
    document.getElementById('edit-budget-amount').value = amount;
    document.getElementById('modal-edit-budget').classList.remove('hidden');
}

document.addEventListener('click', function(e) {
    ['add-budget-cat'].forEach(function(prefix) {
        const trigger  = document.getElementById(prefix + '-trigger');
        const dropdown = document.getElementById(prefix + '-dropdown');
        if (trigger && dropdown && !trigger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>

@endpush