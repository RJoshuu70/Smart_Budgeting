<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function __construct(private BudgetService $budgetService) {}

    public function index()
    {
        $weekStart = $this->budgetService->getWeekStart();
        $weekEnd   = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        // Budget minggu ini milik user
        $budgets = Budget::with('category')
            ->where('user_id', Auth::id())
            ->where('week_start', $weekStart)
            ->get();

        // Hitung spending per budget
        $budgetData = $budgets->map(function ($budget) use ($weekStart) {
            $spent = $this->budgetService->getWeeklySpending(
                Auth::id(),
                $weekStart,
                $budget->category_id
            );
            return [
                'budget'     => $budget,
                'spent'      => $spent,
                'remaining'  => $budget->amount - $spent,
                'percentage' => $budget->amount > 0
                    ? round(($spent / $budget->amount) * 100, 1)
                    : 0,
            ];
        });

        $categories = Category::orderBy('name')->get();

        return view('budgets.index', compact(
            'budgetData', 'categories', 'weekStart', 'weekEnd'
        ));
    }

    public function store(Request $request)
    {
        $weekStart = $this->budgetService->getWeekStart();

        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'amount'      => 'required|numeric|min:1000',
        ], [
            'amount.min' => 'Budget minimal Rp 1.000.',
        ]);

        // Upsert: update kalau sudah ada, insert kalau belum
        Budget::updateOrCreate(
            [
                'user_id'     => Auth::id(),
                'category_id' => $request->category_id ?: null,
                'week_start'  => $weekStart,
            ],
            ['amount' => $request->amount]
        );

        return redirect()->route('budgets.index')
            ->with('success', 'Budget berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $budget = Budget::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $budget->update(['amount' => $request->amount]);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $budget = Budget::where('user_id', Auth::id())->findOrFail($id);
        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget berhasil dihapus!');
    }
}