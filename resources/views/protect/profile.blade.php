@use('App\Support\Money')
@php
    $initials = collect(explode(' ', trim($user->name)))
        ->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
@endphp

<x-layout title="Profile">
    <div class="py-12 px-4 bg-gray-50 min-h-[calc(100vh-80px)]">
        <div class="max-w-4xl mx-auto space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
            @endif

            {{-- Header card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center gap-6">
                <div class="w-24 h-24 rounded-full bg-orange-100 text-orange-600 text-3xl font-bold flex items-center justify-center shrink-0">
                    {{ $initials }}
                </div>

                <div class="flex-grow">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="text-xs font-medium px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-100">{{ $business->name }}</span>
                        <span class="text-xs font-medium px-3 py-1 rounded-full bg-green-50 text-green-700 border border-green-100">Active Merchant</span>
                    </div>
                </div>

                <a href="{{ route('settings') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-5 py-2.5 rounded-lg text-sm self-start">Edit Profile</a>
            </div>

            {{-- Real numbers --}}
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Total customers</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $customerCount }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Credit tracked</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ Money::format($outstanding) }}</p>
                    <p class="text-xs text-gray-500 mt-1">currently owed to you</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Member since</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $user->created_at->format('M Y') }}</p>
                </div>
            </div>

            {{-- Account details --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Account Details</h2>
                    <p class="text-sm text-gray-500">Manage your personal details and shop preferences.</p>
                </div>

                <dl class="grid sm:grid-cols-2 gap-x-8 gap-y-5 p-6 text-sm">
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase">Full name</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase">Phone number</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $business->phone ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase">Shop name</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $business->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase">Business type</dt>
                        <dd class="text-gray-900 mt-0.5">{{ \App\Models\Business::TYPES[$business->business_type] ?? 'Other' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold text-gray-400 uppercase">Location</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $business->address ?: 'Not set' }}</dd>
                    </div>
                </dl>

                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
                    <a href="{{ route('settings') }}#security" class="text-sm font-medium text-orange-600 hover:text-orange-700">Change Password?</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm font-medium px-4 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600">Log Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>