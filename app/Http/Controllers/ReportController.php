<?php

namespace App\Http\Controllers;

use App\Models\CreditTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /** Everyone who currently owes money, biggest balance first. */
    public function outstanding()
    {
        $business = Auth::user()->business;

        $customers = $business->customers()
            ->withSum('transactions as balance', 'amount')
            ->withMax(['transactions as last_payment_at' => fn ($q) => $q->where('type', 'payment')], 'occurred_at')
            ->whereRaw('(select coalesce(sum(amount), 0) from credit_transactions where credit_transactions.customer_id = customers.id) > 0')
            ->orderByDesc('balance')
            ->get();

        return view('reports.outstanding', [
            'customers' => $customers,
            'total' => (int) $customers->sum('balance'),
        ]);
    }

    /** All entries across customers, filterable by date, customer and type. */
    public function transactions(Request $request)
    {
        $business = Auth::user()->business;

        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'customer_id' => ['nullable', Rule::exists('customers', 'id')->where('business_id', $business->id)],
            'type' => ['nullable', Rule::in([
                CreditTransaction::OPENING, CreditTransaction::CREDIT, CreditTransaction::PAYMENT,
                CreditTransaction::ADJUSTMENT, CreditTransaction::REVERSAL,
            ])],
        ]);

        $filtered = CreditTransaction::where('business_id', $business->id)
            ->when($data['from'] ?? null, fn ($q, $d) => $q->where('occurred_at', '>=', \Carbon\Carbon::parse($d)->startOfDay()))
            ->when($data['to'] ?? null, fn ($q, $d) => $q->where('occurred_at', '<=', \Carbon\Carbon::parse($d)->endOfDay()))
            ->when($data['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($data['type'] ?? null, fn ($q, $type) => $q->where('type', $type));

        $given = (int) (clone $filtered)->where('amount', '>', 0)->sum('amount');
        $received = -1 * (int) (clone $filtered)->where('amount', '<', 0)->sum('amount');

        $rows = $filtered->with(['customer', 'creator'])
            ->orderByDesc('occurred_at')->orderByDesc('id')
            ->paginate(25)->withQueryString();

        return view('reports.transactions', [
            'rows' => $rows,
            'given' => $given,
            'received' => $received,
            'customers' => $business->customers()->orderBy('name')->get(['id', 'name']),
            'filters' => $data,
        ]);
    }
}