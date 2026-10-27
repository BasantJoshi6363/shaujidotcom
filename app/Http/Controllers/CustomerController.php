<?php

namespace App\Http\Controllers;

use App\Services\LedgerService;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function __construct(private LedgerService $ledger)
    {
    }

    /** The logged-in user's business. The has-business middleware guarantees it exists. */
    private function business()
    {
        return Auth::user()->business;
    }

public function index(Request $request)
{
    $business = $request->attributes->get('business')
        ?? $request->user()->business;

    abort_if(! $business, 403);

    // view uses `$customer` (singular) as the paginator
    $customer = $business->customers()->paginate(25);

    return view('customer.customers', compact('customer', 'business'));
}

public function search(Request $request)
{
    $business = $request->attributes->get('business')
        ?? $request->user()->business;

    abort_if(! $business, 403);

    $term = $request->query('search-customer', '');

    $customer = $business->customers()
        ->when($term !== '', function ($q) use ($term) {
            $q->where(function ($q) use ($term) {
                $q->where('name',  'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        })
        ->paginate(25);

    return view('customer.customers', compact('customer', 'business'));
}

    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        $business = $this->business();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('customers', 'email')->where('business_id', $business->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['nullable', 'string', 'regex:/^[\d,]{1,12}(\.\d{1,2})?$/'],
        ]);

        $opening = 0;
        if (!empty($data['opening_balance'])) {
            $opening = Money::toPaisa($data['opening_balance']);
            if ($opening === null) {
                throw ValidationException::withMessages(['opening_balance' => 'Enter a valid amount, e.g. 1500 or 1500.50']);
            }
        }

        $customer = DB::transaction(function () use ($business, $data, $opening) {
            $customer = $business->customers()->create(
                Arr::only($data, ['name', 'email', 'phone', 'address']) + ['user_id' => Auth::id()]
            );

            if ($opening > 0) {
                $this->ledger->openingBalance($customer, $opening, Auth::user());
            }

            return $customer;
        });

        return redirect()->route('customers.show', $customer->id)->with('success', 'Customer created successfully.');
    }

    public function edit($id)
    {
        $customer = $this->business()->customers()->findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $business = $this->business();
        $customer = $business->customers()->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('customers', 'email')
                ->where('business_id', $business->id)->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $customer->update($data);

        return redirect()->route('customers')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $customer = $this->business()->customers()->findOrFail($id);

        // Customers with ledger history keep their records: financial history is never erased
        if ($customer->transactions()->exists()) {
            return redirect()->route('customers')
                ->withErrors(['delete' => 'This customer has credit history and cannot be deleted.']);
        }

        $customer->delete();

        return redirect()->route('customers')->with('success', 'Customer deleted successfully.');
    }

  
}