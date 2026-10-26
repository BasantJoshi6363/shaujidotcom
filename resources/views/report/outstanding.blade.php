@use('App\Support\Money')
<x-layout title="Outstanding balances">
    <div class="py-12 px-4 max-w-5xl mx-auto min-h-[calc(100vh-80px)]">

        <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('welcome') }}" class="text-sm text-gray-500 hover:text-orange-600">&larr; Dashboard</a>
                <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mt-2 mb-1">Report</p>
                <h1 class="text-2xl font-bold text-gray-900">Outstanding balances</h1>
                <p class="text-sm text-gray-500 mt-0.5">Customers who currently owe you money.</p>
            </div>
            <button onclick="window.print()"
                class="bg-gray-900 hover:bg-gray-800 text-white font-medium px-5 py-2.5 rounded-lg text-sm print:hidden">
                Print
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Total to collect</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ Money::format($total) }}</p>
            <p class="text-sm text-gray-500">from {{ $customers->count() }} {{ \Illuminate\Support\Str::plural('customer', $customers->count()) }}</p>
        </div>

        @if ($customers->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
                <h3 class="text-base font-semibold text-gray-800">Nobody owes you anything</h3>
                <p class="text-sm text-gray-500 mt-1">All customer balances are settled.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">#</th>
                                <th class="py-3.5 px-6">Customer</th>
                                <th class="py-3.5 px-6">Phone</th>
                                <th class="py-3.5 px-6">Last payment</th>
                                <th class="py-3.5 px-6 text-right">Owes</th>
                                <th class="py-3.5 px-6 print:hidden"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach ($customers as $i => $c)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-3.5 px-6 text-gray-400">{{ $i + 1 }}</td>
                                    <td class="py-3.5 px-6 font-semibold text-gray-900">{{ $c->name }}</td>
                                    <td class="py-3.5 px-6">{{ $c->phone ?? 'N/A' }}</td>
                                    <td class="py-3.5 px-6 text-gray-500">
                                        {{ $c->last_payment_at ? \Carbon\Carbon::parse($c->last_payment_at)->format('d M Y') : 'Never' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right font-semibold text-red-600">{{ Money::format((int) $c->balance) }}</td>
                                    <td class="py-3.5 px-6 text-right print:hidden">
                                        <a href="{{ route('customers.show', $c->id) }}"
                                           class="px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-orange-600 font-medium rounded-lg text-xs">Open ledger</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layout>