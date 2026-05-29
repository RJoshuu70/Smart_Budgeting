<?php

namespace App\Http\Controllers;

use App\Services\BudgetService;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private BudgetService $budgetService) {}

    public function index()
    {
        $user      = Auth::user();
        $weekStart = $this->budgetService->getWeekStart();
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Top 3 kategori pengeluaran minggu ini
        $topCategories = Transaction::with('category')
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        // 5 transaksi terbaru
        $recentTransactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Total pemasukan & pengeluaran minggu ini
        $weeklyIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->sum('amount');

        $weeklyExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->sum('amount');

        return view('dashboard', [
            'budgetStatus'       => $this->budgetService->getBudgetStatus($user->id),
            'topCategories'      => $topCategories,
            'recentTransactions' => $recentTransactions,
            'weeklyIncome'       => $weeklyIncome,
            'weeklyExpense'      => $weeklyExpense,
            'weekStart'          => $weekStart,
            'weekEnd'            => $weekEnd,
        ]);
    }
}