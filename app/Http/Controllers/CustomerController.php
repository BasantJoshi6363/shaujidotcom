<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $customer = Auth::user()->customers;
        return view("customer.customers", compact("customer"));
    }
    public function store(Request $request)
    {
        // Logic to store a new customer in the database

        $customer_info = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'user_id' => Auth::id(),
            'name' => $customer_info['name'],
            'email' => $customer_info['email'] ?? null,
            'phone' => $customer_info['phone'] ?? null,
            'address' => $customer_info['address'] ?? null,
        ]);

        return redirect()->route('welcome')->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        // Logic to retrieve and display a specific customer by ID
    }

    public function create()
    {
        return view('customer.create');
    }

    public function destroy($id)
    {
       
        $customer = Auth::user()->customers()->findOrFail($id);
        $customer->delete();
        return redirect()->route('customers')->with('success', 'Customer deleted successfully.');
    }

    public function edit($id)
    {
        $customer = Auth::user()->customers()->findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Auth::user()->customers()->findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer->update($validatedData);

        return redirect()->route('customers')->with('success', 'Customer updated successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->query('search-customer');

        $customer = Auth::user()->customers()
        ->where('name','LIKE','%'. $search .'%')
        ->orWhere('email','LIKE','%'. $search .'%')
        ->get();

        return view("customer.customers", compact("customer"));
    }
}
