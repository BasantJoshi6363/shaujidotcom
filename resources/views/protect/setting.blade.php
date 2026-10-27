@php
    $tab = session('tab')
        ?? ($errors->getBag('password')->any() ? 'security'
        : ($errors->getBag('account')->any() ? 'danger' : 'shop'));
    $field = 'w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500';
    $label = 'block text-sm font-medium text-gray-700 mb-1';
@endphp

<x-layout title="Settings">
    <style>
        .tab-active { border-left: 4px solid #ea580c; background: #fff; font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
    </style>

    <div class="py-12 px-4 bg-gray-50 min-h-[calc(100vh-80px)]">
        <div class="max-w-5xl mx-auto">

            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">Preferences</p>
            <h1 class="text-2xl font-bold text-gray-900">Account Settings</h1>
            <p class="text-sm text-gray-500 mt-0.5 mb-8">Manage your shop profile, reminders, and security options.</p>

            @if (session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
            @endif

            <div class="grid md:grid-cols-3 gap-6 items-start">

                {{-- Sidebar --}}
                <nav class="space-y-1 text-sm">
                    <button type="button" data-tab="shop"     class="tab-btn w-full text-left px-5 py-3 rounded-lg text-gray-700 hover:bg-gray-100">Shop Profile</button>
                    <button type="button" data-tab="sms"      class="tab-btn w-full text-left px-5 py-3 rounded-lg text-gray-700 hover:bg-gray-100">SMS Reminders (For Premium Users)</button>
                    <button type="button" data-tab="security" class="tab-btn w-full text-left px-5 py-3 rounded-lg text-gray-700 hover:bg-gray-100">Security</button>
                    <button type="button" data-tab="danger"   class="tab-btn w-full text-left px-5 py-3 rounded-lg text-red-600 hover:bg-red-50">Danger Zone</button>
                </nav>

                <div class="md:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">

                    {{-- Shop profile --}}
                    <section data-panel="shop" class="hidden">
                        <h2 class="font-semibold text-gray-900 text-lg">Shop Details</h2>
                        <p class="text-sm text-gray-500 mb-6">Update your store identity shown to customers.</p>

                        <form method="POST" action="{{ route('settings.shop.update') }}" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="{{ $label }}">Shop Name</label>
                                <input name="name" value="{{ old('name', $business->name) }}" class="{{ $field }}">
                                @error('name', 'shop') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Business Type</label>
                                <select name="business_type" class="{{ $field }} bg-white">
                                    @foreach ($types as $value => $text)
                                        <option value="{{ $value }}" @selected(old('business_type', $business->business_type) === $value)>{{ $text }}</option>
                                    @endforeach
                                </select>
                                @error('business_type', 'shop') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Owner Full Name</label>
                                <input name="owner_name" value="{{ old('owner_name', $user->name) }}" class="{{ $field }}">
                                @error('owner_name', 'shop') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Phone Number</label>
                                <input name="phone" value="{{ old('phone', $business->phone) }}" placeholder="+977 98XXXXXXXX" class="{{ $field }}">
                                @error('phone', 'shop') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Address</label>
                                <input name="address" value="{{ old('address', $business->address) }}" placeholder="Town, district" class="{{ $field }}">
                                @error('address', 'shop') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <button class="bg-orange-600 hover:bg-orange-700 text-white font-medium px-6 py-3 rounded-lg text-sm">Save Profile</button>
                        </form>
                    </section>

                    {{-- SMS reminders (placeholder: messaging integrations are deferred) --}}
                    <section data-panel="sms" class="hidden">
                        <h2 class="font-semibold text-gray-900 text-lg">SMS Reminders</h2>
                        <p class="text-sm text-gray-500 mb-6">Automatic payment reminders to customers who owe you.</p>
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                            <p class="font-medium text-gray-800">Coming soon for Premium users</p>
                            <p class="text-sm text-gray-500 mt-1">Until then, use the Outstanding report to see who to follow up with.</p>
                            <a href="{{ route('reports.outstanding') }}" class="inline-block mt-4 text-sm font-medium text-orange-600 hover:text-orange-700">Open Outstanding report &rarr;</a>
                        </div>
                    </section>

                    {{-- Security --}}
                    <section data-panel="security" class="hidden">
                        <h2 class="font-semibold text-gray-900 text-lg">Change Password</h2>
                        <p class="text-sm text-gray-500 mb-6">Use at least 8 characters.</p>

                        <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="{{ $label }}">Current Password</label>
                                <input type="password" name="current_password" autocomplete="current-password" class="{{ $field }}">
                                @error('current_password', 'password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">New Password</label>
                                <input type="password" name="password" autocomplete="new-password" class="{{ $field }}">
                                @error('password', 'password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Confirm New Password</label>
                                <input type="password" name="password_confirmation" autocomplete="new-password" class="{{ $field }}">
                            </div>

                            <button class="bg-orange-600 hover:bg-orange-700 text-white font-medium px-6 py-3 rounded-lg text-sm">Update Password</button>
                        </form>
                    </section>

                    {{-- Danger zone --}}
                    <section data-panel="danger" class="hidden">
                        <h2 class="font-semibold text-red-600 text-lg">Danger Zone</h2>
                        <p class="text-sm text-gray-500 mb-6">
                            Deleting your account permanently erases your shop, all customers, and their complete credit history.
                            This cannot be undone.
                        </p>

                        <form method="POST" action="{{ route('settings.account.destroy') }}" class="space-y-5"
                              onsubmit="return confirm('This permanently deletes your account and ALL your data. Continue?');">
                            @csrf
                            @method('DELETE')

                            <div>
                                <label class="{{ $label }}">Your Password</label>
                                <input type="password" name="password" autocomplete="current-password" class="{{ $field }}">
                                @error('password', 'account') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Type <span class="font-mono font-bold">DELETE</span> to confirm</label>
                                <input name="confirmation" autocomplete="off" class="{{ $field }}">
                                @error('confirmation', 'account') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <button class="bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-3 rounded-lg text-sm">Delete My Account</button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script>
        const known = ['shop', 'sms', 'security', 'danger'];
        const fromHash = location.hash.replace('#', '');
        const start = known.includes(fromHash) ? fromHash : @json($tab);

        function showTab(name) {
            document.querySelectorAll('[data-panel]').forEach(p => p.classList.toggle('hidden', p.dataset.panel !== name));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.toggle('tab-active', b.dataset.tab === name));
        }

        document.querySelectorAll('.tab-btn').forEach(b => b.addEventListener('click', () => showTab(b.dataset.tab)));
        showTab(start);
    </script>
</x-layout>