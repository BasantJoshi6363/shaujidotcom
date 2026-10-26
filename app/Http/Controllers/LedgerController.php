<?php

namespace App\Http\Controllers;

use App\Models\CreditTransaction;
use App\Services\LedgerService;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LedgerController extends Controller
{
    public function __construct(private LedgerService $ledger)
    {
    }

    /** Only customers of the logged-in user's business can ever be loaded. */
    private function customer($id)
    {
        return Auth::user()->business->customers()->findOrFail($id);
    }

    public function show(Request $request, $id)
    {
        $customer = $this->customer($id);

        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : null;
        $to = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : null;

        $query = $customer->transactions()->with('creator')->orderBy('occurred_at')->orderBy('id');
        if ($from) {
            $query->where('occurred_at', '>=', $from);
        }
        if ($to) {
            $query->where('occurred_at', '<=', $to);
        }

        // When a start date is chosen, the running balance starts from everything before it
        $running = $from
            ? (int) $customer->transactions()->where('occurred_at', '<', $from)->sum('amount')
            : 0;
        $broughtForward = $running;

        $rows = $query->get()->map(function ($t) use (&$running) {
            $running += $t->amount;
            $t->running_balance = $running;
            return $t;
        });

        return view('customer.show', [
            'customer' => $customer,
            'rows' => $rows,
            'balance' => $this->ledger->balance($customer),
            'broughtForward' => $broughtForward,
            'reversedIds' => $customer->transactions()->whereNotNull('reversal_of_id')->pluck('reversal_of_id')->all(),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ]);
    }

    public function credit(Request $request, $id)
    {
        $customer = $this->customer($id);
        [$paisa, $note, $at, $key] = $this->entryInput($request, 'credit');

        $this->ledger->credit($customer, $paisa, Auth::user(), $note, $at, $key);

        return redirect()->route('customers.show', $customer->id)->with('success', 'Credit recorded.');
    }

    public function payment(Request $request, $id)
    {
        $customer = $this->customer($id);
        [$paisa, $note, $at, $key] = $this->entryInput($request, 'payment');

        $this->ledger->payment($customer, $paisa, Auth::user(), $note, $at, $key);

        return redirect()->route('customers.show', $customer->id)->with('success', 'Payment recorded.');
    }

    public function reverse(Request $request, $id)
    {
        $tx = Auth::user()->business->transactions()->findOrFail($id);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:200'],
            'idempotency_key' => ['required', 'string', 'max:64'],
        ]);

        $this->ledger->reverse($tx, Auth::user(), $data['reason'] ?? null, $data['idempotency_key']);

        return redirect()->route('customers.show', $tx->customer_id)->with('success', 'Entry reversed.');
    }

    /** Validates the credit/payment form. $bag keeps the two forms' errors apart. */
    private function entryInput(Request $request, string $bag): array
    {
        $data = $request->validateWithBag($bag, [
            'amount' => ['required', 'string', 'regex:/^[\d,]{1,12}(\.\d{1,2})?$/'],
            'description' => ['nullable', 'string', 'max:255'],
            'occurred_on' => ['nullable', 'date', 'before_or_equal:today'],
            'idempotency_key' => ['required', 'string', 'max:64'],
        ]);

        $paisa = Money::toPaisa($data['amount']);

        if (!$paisa) {
            throw ValidationException::withMessages(['amount' => 'Enter a valid amount, e.g. 1500 or 1500.50'])
                ->errorBag($bag);
        }

        $at = !empty($data['occurred_on'])
            ? Carbon::parse($data['occurred_on'])->setTimeFrom(now())
            : null;

        return [$paisa, $data['description'] ?? null, $at, $data['idempotency_key']];
    }
}