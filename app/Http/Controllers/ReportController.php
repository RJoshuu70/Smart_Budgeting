<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period    = $request->get('period', 'week');
        $userId    = Auth::id();

        [$startDate, $endDate] = $this->getDateRange($period);

        // Total pemasukan & pengeluaran
        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $totalExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Pengeluaran per kategori (untuk pie chart)
        $byCategory = Transaction::with('category')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        // Pengeluaran harian (untuk bar chart)
        $dailyExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

        // Generate semua tanggal dalam range
        $dates  = [];
        $totals = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key      = $current->format('Y-m-d');
            $dates[]  = $current->translatedFormat('d M');
            $totals[] = isset($dailyExpense[$key])
                ? (float) $dailyExpense[$key]->total
                : 0;
            $current->addDay();
        }

        return view('reports.index', compact(
            'totalIncome', 'totalExpense', 'byCategory',
            'dates', 'totals', 'period', 'startDate', 'endDate'
        ));
    }

    private function getDateRange(string $period): array
    {
        return match($period) {
            'month' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            default => [ // week
                Carbon::now()->startOfWeek(Carbon::MONDAY),
                Carbon::now()->endOfWeek(Carbon::SUNDAY),
            ],
        };
    }
}