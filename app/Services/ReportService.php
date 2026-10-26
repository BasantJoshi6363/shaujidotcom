<?php

namespace App\Services;

use App\Models\Business;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Read-only numbers for the dashboard and reports.
 * The future mobile API will call the same methods.
 */
class ReportService
{
    public function dashboard(Business $business): array
    {
        // customer_id => balance in paisa (positive = owes you, negative = paid in advance)
        $balances = $business->transactions()
            ->selectRaw('customer_id, SUM(amount) as balance')
            ->groupBy('customer_id')
            ->pluck('balance', 'customer_id')
            ->map(fn ($b) => (int) $b);

        $owed = $balances->filter(fn ($b) => $b > 0);
        $advance = $balances->filter(fn ($b) => $b < 0);

        $top = $owed->sortDesc()->take(5);
        $customers = $business->customers()->whereIn('id', $top->keys()->all())->get()->keyBy('id');

        $topDebtors = $top
            ->map(fn ($balance, $id) => ['customer' => $customers->get($id), 'balance' => $balance])
            ->filter(fn ($row) => $row['customer'])
            ->values();

        return [
            'total_outstanding' => (int) $owed->sum(),
            'debtor_count' => $owed->count(),
            'advance_total' => abs((int) $advance->sum()),
            'customer_count' => $business->customers()->count(),
            'today' => $this->movement($business, now()->startOfDay(), now()->endOfDay()),
            'month' => $this->movement($business, now()->startOfMonth(), now()->endOfMonth()),
            'top_debtors' => $topDebtors,
            'recent' => $business->transactions()->with('customer')
                ->orderByDesc('occurred_at')->orderByDesc('id')->limit(5)->get(),
        ];
    }

    /**
     * Credit given and payments received in a period (paisa).
     * A reversal is counted against the kind of entry it cancels,
     * so reversing a payment lowers "payments", not raises "credit".
     * Opening balances are not counted as new credit.
     */
    public function movement(Business $business, CarbonInterface $from, CarbonInterface $to): array
    {
        $totals = DB::table('credit_transactions as t')
            ->leftJoin('credit_transactions as o', 'o.id', '=', 't.reversal_of_id')
            ->where('t.business_id', $business->id)
            ->whereBetween('t.occurred_at', [$from, $to])
            ->selectRaw('COALESCE(o.type, t.type) as effective_type, SUM(t.amount) as total')
            ->groupBy('effective_type')
            ->pluck('total', 'effective_type');

        return [
            'credit' => (int) ($totals['credit'] ?? 0),
            'payments' => -1 * (int) ($totals['payment'] ?? 0),
        ];
    }
}