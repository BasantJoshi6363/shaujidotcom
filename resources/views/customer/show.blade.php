@use('App\Support\Money')
@php
    $labels = [
        'opening_balance' => ['Opening balance', 'bg-gray-100 text-gray-700'],
        'credit'          => ['Credit given', 'bg-red-50 text-red-700'],
        'payment'         => ['Payment', 'bg-green-50 text-green-700'],
        'adjustment'      => ['Adjustment', 'bg-yellow-50 text-yellow-700'],
        'reversal'        => ['Reversal', 'bg-purple-50 text-purple-700'],
    ];
    $inputClass = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500';
@endphp

<x-layout title="{{ $customer->name }}">
    <div class="bg-gray-50 min-h-[calc(100vh-80px)] py-10 px-4">
        <div class="max-w-5xl mx-auto space-y-6">

            {{-- Header --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <a href="{{ route('customers') }}" class="text-sm text-gray-500 hover:text-orange-600">&larr; All customers</a>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $customer->phone }} {{ $customer->address ? '· ' . $customer->address : '' }}</p>
                </div>
                <a href="{{ route('edit-customer', $customer->id) }}"
                   class="text-sm bg-white border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-lg">Edit customer</a>
            </div>

            @if (session('success'))
                <div class="text-sm text-green-700 bg-green-50 border border-green-100 rounded-lg py-2 px-3">{{ session('success') }}</div>
            @endif
            @error('transaction')
                <div class="text-sm text-red-700 bg-red-50 border border-red-100 rounded-lg py-2 px-3">{{ $message }}</div>
            @enderror

            {{-- Balance --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Outstanding balance</p>
                @if ($balance > 0)
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ Money::format($balance) }}</p>
                    <p class="text-sm text-gray-500">This customer owes you.</p>
                @elseif ($balance < 0)
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ Money::format(abs($balance)) }}</p>
                    <p class="text-sm text-gray-500">Paid in advance. You owe this customer.</p>
                @else
                    <p class="text-3xl font-bold text-gray-900 mt-1">NPR 0</p>
                    <p class="text-sm text-gray-500">All settled.</p>
                @endif
            </div>

            {{-- Entry forms --}}
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Give credit --}}
                <form method="POST" action="{{ route('customers.credit', $customer->id) }}"
                      class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
                    @csrf
                    <input type="hidden" name="idempotency_key" value="{{ \Illuminate\Support\Str::uuid() }}">
                    <h2 class="font-semibold text-gray-900">Give credit</h2>
                    @if ($errors->credit->any())
                        <p class="text-sm text-red-600">{{ $errors->credit->first() }}</p>
                    @endif
                    <input name="amount" inputmode="decimal" placeholder="Amount (NPR)" value="{{ old('amount') }}" class="{{ $inputClass }}">
                    <input name="description" placeholder="Note (optional)" class="{{ $inputClass }}">
                    <input type="date" name="occurred_on" max="{{ now()->toDateString() }}" class="{{ $inputClass }}">
                    <button class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 rounded-lg text-sm">Add credit</button>
                </form>

                {{-- Receive payment --}}
                <form method="POST" action="{{ route('customers.payments', $customer->id) }}"
                      class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
                    @csrf
                    <input type="hidden" name="idempotency_key" value="{{ \Illuminate\Support\Str::uuid() }}">
                    <h2 class="font-semibold text-gray-900">Receive payment</h2>
                    @if ($errors->payment->any())
                        <p class="text-sm text-red-600">{{ $errors->payment->first() }}</p>
                    @endif
                    <input name="amount" inputmode="decimal" placeholder="Amount (NPR)" class="{{ $inputClass }}">
                    <input name="description" placeholder="Note (optional)" class="{{ $inputClass }}">
                    <input type="date" name="occurred_on" max="{{ now()->toDateString() }}" class="{{ $inputClass }}">
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-lg text-sm">Record payment</button>
                </form>
            </div>

            {{-- Ledger --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
                    <h2 class="font-semibold text-gray-900">Ledger</h2>
                    <form method="GET" class="flex flex-wrap items-end gap-2 text-sm">
                        <label class="text-gray-500">From <input type="date" name="from" value="{{ $from }}" class="ml-1 px-2 py-1.5 border border-gray-200 rounded-lg"></label>
                        <label class="text-gray-500">To <input type="date" name="to" value="{{ $to }}" class="ml-1 px-2 py-1.5 border border-gray-200 rounded-lg"></label>
                        <button class="bg-gray-900 text-white px-3 py-1.5 rounded-lg">Filter</button>
                        @if ($from || $to)
                            <a href="{{ route('customers.show', $customer->id) }}" class="text-gray-500 py-1.5">Clear</a>
                        @endif
                    </form>
                </div>

                @error('to') <p class="text-sm text-red-600 mb-2">{{ $message }}</p> @enderror

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-gray-400 border-b border-gray-100">
                                <th class="py-2 pr-3">Date</th>
                                <th class="py-2 pr-3">Details</th>
                                <th class="py-2 pr-3 text-right">Credit given</th>
                                <th class="py-2 pr-3 text-right">Received</th>
                                <th class="py-2 pr-3 text-right">Balance</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($from && $broughtForward !== 0)
                                <tr class="border-b border-gray-50 text-gray-500">
                                    <td class="py-2 pr-3" colspan="4">Balance brought forward</td>
                                    <td class="py-2 pr-3 text-right font-medium">{{ Money::format($broughtForward) }}</td>
                                    <td></td>
                                </tr>
                            @endif

                            @forelse ($rows as $t)
                                @php $isReversed = in_array($t->id, $reversedIds); @endphp
                                <tr class="border-b border-gray-50 {{ $isReversed ? 'text-gray-400' : '' }}">
                                    <td class="py-2.5 pr-3 whitespace-nowrap">{{ $t->occurred_at->format('d M Y') }}</td>
                                    <td class="py-2.5 pr-3">
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full {{ $labels[$t->type][1] ?? '' }}">{{ $labels[$t->type][0] ?? $t->type }}</span>
                                        @if ($isReversed) <span class="text-xs text-purple-600">(reversed)</span> @endif
                                        @if ($t->description && $t->type !== 'opening_balance') <span class="ml-1">{{ $t->description }}</span> @endif
                                        <span class="block text-xs text-gray-400">by {{ $t->creator->name ?? 'unknown' }}</span>
                                    </td>
                                    <td class="py-2.5 pr-3 text-right text-red-600">{{ $t->amount > 0 ? Money::format($t->amount) : '' }}</td>
                                    <td class="py-2.5 pr-3 text-right text-green-600">{{ $t->amount < 0 ? Money::format(abs($t->amount)) : '' }}</td>
                                    <td class="py-2.5 pr-3 text-right font-medium">{{ Money::format($t->running_balance) }}</td>
                                    <td class="py-2.5 text-right">
                                        @if ($t->type !== 'reversal' && !$isReversed)
                                            <details class="relative">
                                                <summary class="text-xs text-gray-400 hover:text-red-600 cursor-pointer list-none">Reverse</summary>
                                                <form method="POST" action="{{ route('transactions.reverse', $t->id) }}"
                                                      class="absolute right-0 z-10 mt-1 w-56 bg-white border border-gray-200 rounded-lg shadow p-3 space-y-2">
                                                    @csrf
                                                    <input type="hidden" name="idempotency_key" value="{{ \Illuminate\Support\Str::uuid() }}">
                                                    <input name="reason" placeholder="Reason (optional)" class="{{ $inputClass }}">
                                                    <button class="w-full bg-red-600 text-white text-xs py-2 rounded-lg">Confirm reversal</button>
                                                </form>
                                            </details>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-8 text-center text-gray-400">No entries yet. Add credit or record a payment above.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout>