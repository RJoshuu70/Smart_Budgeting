<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use Carbon\Carbon;

class BudgetService
{
    public function getWeekStart(?Carbon $date = null): Carbon
    {
        return ($date ?? Carbon::today())->startOfWeek(Carbon::MONDAY);
    }

    public function getWeeklySpending(int $userId, Carbon $weekStart, ?int $categoryId = null): float
    {
        $query = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [
                $weekStart,
                $weekStart->copy()->endOfWeek(Carbon::SUNDAY)
            ]);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return (float) $query->sum('amount');
    }

    public function getCurrentBudget(int $userId, ?int $categoryId = null): ?Budget
    {
        return Budget::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('week_start', $this->getWeekStart())
            ->first();
    }

    public function getBudgetStatus(int $userId): array
    {
        $weekStart = $this->getWeekStart();
        $budget    = $this->getCurrentBudget($userId);
        $spent     = $this->getWeeklySpending($userId, $weekStart);

        if (!$budget) {
            return ['has_budget' => false, 'spent' => $spent];
        }

        $remaining  = $budget->amount - $spent;
        $percentage = ($spent / $budget->amount) * 100;

        return [
            'has_budget' => true,
            'budget'     => $budget->amount,
            'spent'      => $spent,
            'remaining'  => $remaining,
            'percentage' => round($percentage, 1),
            'status'     => match(true) {
                $percentage >= 100 => 'over',
                $percentage >= 75  => 'warning',
                default            => 'safe',
            },
        ];
    }
}