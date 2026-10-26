@use('App\Support\Money')
<x-layout title="Home">
    @auth
        <div class="py-12 px-4 bg-gray-50 min-h-[calc(100vh-80px)]">
            <div class="max-w-6xl mx-auto space-y-8">

                {{-- Header --}}
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                            Dashboard &middot; {{ $business->name }}
                        </p>
                        <h2 class="text-2xl font-bold text-gray-900">
                            Welcome, <span class="text-orange-600">{{ auth()->user()->name }}</span>!
                        </h2>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('create-customer') }}"
                           class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 px-5 rounded-lg text-sm shadow-sm inline-flex items-center gap-2">
                            <i class="fa-solid fa-user-plus text-xs"></i><span>Create Customer</span>
                        </a>
                        <a href="{{ route('customers') }}"
                           class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-5 rounded-lg text-sm inline-flex items-center gap-2">
                            <i class="fa-solid fa-users text-xs"></i><span>All Customers</span>
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
                @endif

                {{-- Main numbers --}}
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Total outstanding</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ Money::format($stats['total_outstanding']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">from {{ $stats['debtor_count'] }} {{ \Illuminate\Support\Str::plural('customer', $stats['debtor_count']) }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Credit given today</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ Money::format($stats['today']['credit']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">this month {{ Money::format($stats['month']['credit']) }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Payments today</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ Money::format($stats['today']['payments']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">this month {{ Money::format($stats['month']['payments']) }}</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Customers</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['customer_count'] }}</p>
                        @if ($stats['advance_total'] > 0)
                            <p class="text-xs text-gray-500 mt-1">{{ Money::format($stats['advance_total']) }} paid in advance</p>
                        @endif
                    </div>
                </div>

                <div class="grid lg:grid-cols-2 gap-6">
                    {{-- Top debtors --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900">Who owes the most</h3>
                            <a href="{{ route('reports.outstanding') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Full report &rarr;</a>
                        </div>
                        @forelse ($stats['top_debtors'] as $row)
                            <a href="{{ route('customers.show', $row['customer']->id) }}"
                               class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg">
                                <span class="text-sm font-medium text-gray-800">{{ $row['customer']->name }}</span>
                                <span class="text-sm font-semibold text-red-600">{{ Money::format($row['balance']) }}</span>
                            </a>
                        @empty
                            <p class="text-sm text-gray-400 py-6 text-center">No pending balances yet.</p>
                        @endforelse
                    </div>

                    {{-- Recent activity --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900">Recent activity</h3>
                            <a href="{{ route('transactions') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">All transactions &rarr;</a>
                        </div>
                        @forelse ($stats['recent'] as $t)
                            <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                                <div>
                                    <a href="{{ route('customers.show', $t->customer_id) }}" class="text-sm font-medium text-gray-800 hover:text-orange-600">{{ $t->customer->name }}</a>
                                    <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $t->type)) }} &middot; {{ $t->occurred_at->format('d M') }}</p>
                                </div>
                                <span class="text-sm font-semibold {{ $t->amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $t->amount > 0 ? '+' : '-' }}{{ Money::format(abs($t->amount)) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 py-6 text-center">No activity yet. Open a customer to add credit or a payment.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Guest View -->
        <main>
            <x-hero />
            <x-features />
            <x-stats-testimonial />
            <x-faq />
            <x-pricing />
        </main>
    @endauth
</x-layout>