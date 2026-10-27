<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $business = $request->attributes->get('business')
            ?? $request->user()->business;

        abort_if(! $business, 403);

        $customers = $business->customers()
            ->withSum('transactions as computed_balance', 'amount')
            ->get();

        $debtors   = $customers->filter(fn ($c) => (int) $c->computed_balance > 0);
        $advancers = $customers->filter(fn ($c) => (int) $c->computed_balance < 0);

        $today      = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $tx = $business->transactions();

        $stats = [
            'total_outstanding' => (int) $debtors->sum('computed_balance'),
            'debtor_count'      => $debtors->count(),
            'customer_count'    => $customers->count(),
            'advance_total'     => (int) abs($advancers->sum('computed_balance')),
            'today' => [
                'credit'   => (int) $tx->clone()->where('type', 'credit')->whereDate('occurred_at', $today)->sum('amount'),
                'payments' => (int) abs($tx->clone()->where('type', 'payment')->whereDate('occurred_at', $today)->sum('amount')),
            ],
            'month' => [
                'credit'   => (int) $tx->clone()->where('type', 'credit')->where('occurred_at', '>=', $monthStart)->sum('amount'),
                'payments' => (int) abs($tx->clone()->where('type', 'payment')->where('occurred_at', '>=', $monthStart)->sum('amount')),
            ],
            // view does $row['customer']->id / $row['customer']->name / $row['balance']
            'top_debtors' => $debtors
                ->sortByDesc('computed_balance')
                ->take(5)
                ->map(fn ($c) => [
                    'customer' => $c,
                    'balance'  => (int) $c->computed_balance,
                ])
                ->values(),
            // view does $t->customer->name
            'recent' => $tx->clone()->with('customer')->latest('occurred_at')->take(10)->get(),
        ];

        return view('welcome', compact('business', 'stats'));
    }
}