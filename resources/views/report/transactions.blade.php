@use('App\Support\Money')
@php
    $labels = [
        'opening_balance' => ['Opening balance', 'bg-gray-100 text-gray-700'],
        'credit'          => ['Credit given', 'bg-red-50 text-red-700'],
        'payment'         => ['Payment', 'bg-green-50 text-green-700'],
        'adjustment'      => ['Adjustment', 'bg-yellow-50 text-yellow-700'],
        'reversal'        => ['Reversal', 'bg-purple-50 text-purple-700'],
    ];
    $field = 'px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500';
    $hasFilter = collect($filters)->filter()->isNotEmpty();
@endphp

<x-layout title="Transactions">
    <div class="py-12 px-4 max-w-6xl mx-auto min-h-[calc(100vh-80px)]">

        <div class="mb-8">
            <a href="{{ route('welcome') }}" class="text-sm text-gray-500 hover:text-orange-600">&larr; Dashboard</a>
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mt-2 mb-1">Report</p>
            <h1 class="text-2xl font-bold text-gray-900">All transactions</h1>
            <p class="text-sm text-gray-500 mt-0.5">Every credit and payment across your customers.</p>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap items-end gap-3">
            <label class="text-xs text-gray-500">From<br>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="{{ $field }}"></label>
            <label class="text-xs text-gray-500">To<br>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="{{ $field }}"></label>
            <label class="text-xs text-gray-500">Customer<br>
                <select name="customer_id" class="{{ $field }} bg-white">
                    <option value="">All customers</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}" @selected(($filters['customer_id'] ?? null) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select></label>
            <label class="text-xs text-gray-500">Type<br>
                <select name="type" class="{{ $field }} bg-white">
                    <option value="">All types</option>
                    @foreach ($labels as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['type'] ?? null) === $value)>{{ $label[0] }}</option>
                    @endforeach
                </select></label>
            <button class="bg-gray-900 hover:bg-gray-800 text-white font-medium px-5 py-2 rounded-lg text-sm">Filter</button>
            @if ($hasFilter)
                <a href="{{ route('transactions') }}" class="text-sm text-gray-500 py-2">Clear</a>
            @endif
            @error('to') <p class="w-full text-sm text-red-600">{{ $message }}</p> @enderror
            @error('customer_id') <p class="w-full text-sm text-red-600">{{ $message }}</p> @enderror
        </form>

        {{-- Totals for the current filter --}}
        <div class="grid sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Credit given</p>
                <p class="text-xl font-bold text-red-600 mt-1">{{ Money::format($given) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Payments received</p>
                <p class="text-xl font-bold text-green-600 mt-1">{{ Money::format($received) }}</p>
            </div>
        </div>

        @if ($rows->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
                <h3 class="text-base font-semibold text-gray-800">No transactions found</h3>
                <p class="text-sm text-gray-500 mt-1">Try changing the filters.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="py-3.5 px-5">Date</th>
                                <th class="py-3.5 px-5">Customer</th>
                                <th class="py-3.5 px-5">Details</th>
                                <th class="py-3.5 px-5 text-right">Credit given</th>
                                <th class="py-3.5 px-5 text-right">Received</th>
                                <th class="py-3.5 px-5">By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach ($rows as $t)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-3 px-5 whitespace-nowrap">{{ $t->occurred_at->format('d M Y') }}</td>
                                    <td class="py-3 px-5">
                                        <a href="{{ route('customers.show', $t->customer_id) }}" class="font-semibold text-gray-900 hover:text-orange-600">{{ $t->customer->name }}</a>
                                    </td>
                                    <td class="py-3 px-5">
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full {{ $labels[$t->type][1] ?? '' }}">{{ $labels[$t->type][0] ?? $t->type }}</span>
                                        @if ($t->description && $t->type !== 'opening_balance') <span class="ml-1 text-gray-600">{{ $t->description }}</span> @endif
                                    </td>
                                    <td class="py-3 px-5 text-right text-red-600">{{ $t->amount > 0 ? Money::format($t->amount) : '' }}</td>
                                    <td class="py-3 px-5 text-right text-green-600">{{ $t->amount < 0 ? Money::format(abs($t->amount)) : '' }}</td>
                                    <td class="py-3 px-5 text-gray-500">{{ $t->creator->name ?? 'unknown' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">{{ $rows->links() }}</div>
        @endif
    </div>
</x-layout>