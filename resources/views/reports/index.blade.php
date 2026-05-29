@extends('layouts.app')

@section('title', 'Laporan - Smart Budgeting')

@section('content')

{{-- Filter Period --}}
<div class="flex gap-3 mb-6">
    <a href="{{ route('reports.index', ['period' => 'week']) }}"
       class="px-4 py-2 rounded-xl text-sm font-medium transition-colors
              {{ $period === 'week' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }}">
        Minggu Ini
    </a>
    <a href="{{ route('reports.index', ['period' => 'month']) }}"
       class="px-4 py-2 rounded-xl text-sm font-medium transition-colors
              {{ $period === 'month' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }}">
        Bulan Ini
    </a>
</div>

<p class="text-sm text-gray-500 mb-5">
    {{ $startDate->translatedFormat('d M Y') }} – {{ $endDate->translatedFormat('d M Y') }}
</p>

{{-- Summary Card --}}
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
        <p class="text-xs text-green-600 font-medium mb-1">Total Pemasukan</p>
        <p class="text-lg font-bold text-green-700">
            Rp {{ number_format($totalIncome, 0, ',', '.') }}
        </p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <p class="text-xs text-red-600 font-medium mb-1">Total Pengeluaran</p>
        <p class="text-lg font-bold text-red-700">
            Rp {{ number_format($totalExpense, 0, ',', '.') }}
        </p>
    </div>
</div>

{{-- Saldo / Selisih --}}
@php $balance = $totalIncome - $totalExpense; @endphp
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
    <p class="text-sm text-gray-500 mb-1">Selisih (Pemasukan - Pengeluaran)</p>
    <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
        {{ $balance >= 0 ? '+' : '' }}Rp {{ number_format($balance, 0, ',', '.') }}
    </p>
</div>

{{-- Bar Chart: Pengeluaran Harian --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <h2 class="font-semibold text-gray-700 mb-4">📊 Pengeluaran Harian</h2>
    @if(array_sum($totals) > 0)
        <canvas id="barChart" height="120"></canvas>
    @else
        <div class="text-center py-8 text-gray-300">
            <p class="text-4xl mb-2">📊</p>
            <p class="text-sm">Belum ada data pengeluaran</p>
        </div>
    @endif
</div>

{{-- Pie Chart: Per Kategori --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <h2 class="font-semibold text-gray-700 mb-4">🍩 Pengeluaran per Kategori</h2>
    @if($byCategory->count() > 0)
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-full md:w-1/2">
                <canvas id="pieChart" height="200"></canvas>
            </div>
            <div class="w-full md:w-1/2 space-y-2">
                @foreach($byCategory as $item)
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2">
                        <span>{{ $item->category->icon ?? '💸' }}</span>
                        <span class="text-gray-700">{{ $item->category->name ?? 'Lainnya' }}</span>
                    </div>
                    <span class="font-semibold text-red-600">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-8 text-gray-300">
            <p class="text-4xl mb-2">🍩</p>
            <p class="text-sm">Belum ada data kategori</p>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data dari Laravel
    const dates  = @json($dates);
    const totals = @json($totals);

    const categoryLabels = @json($byCategory->map(fn($i) => ($i->category->icon ?? '💸') . ' ' . ($i->category->name ?? 'Lainnya')));
    const categoryTotals = @json($byCategory->pluck('total')->map(fn($v) => (float)$v));
    const categoryColors = @json($byCategory->map(fn($i) => $i->category->color ?? '#94a3b8'));

    // Bar Chart
    @if(array_sum($totals) > 0)
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: dates,
            datasets: [{
                label: 'Pengeluaran (Rp)',
                data: totals,
                backgroundColor: '#6366f1',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => 'Rp ' + val.toLocaleString('id-ID')
                    }
                }
            }
        }
    });
    @endif

    // Pie Chart
    @if($byCategory->count() > 0)
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryTotals,
                backgroundColor: categoryColors,
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush