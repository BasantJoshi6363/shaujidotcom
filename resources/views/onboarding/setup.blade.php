<x-layout title="Set up your shop">
    @php
        $startStep = ($errors->has('phone') || $errors->has('address'))
            && !$errors->has('name') && !$errors->has('business_type') ? 2 : 1;
    @endphp

    <div class="py-16 px-4 flex items-center justify-center min-h-[calc(100vh-80px)] bg-gray-50">
        <div class="w-full max-w-lg bg-white p-8 rounded-2xl shadow-sm border border-gray-100">

            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                Step <span id="step-num">{{ $startStep }}</span> of 2
            </p>
            <h2 class="text-2xl font-bold text-gray-900 mb-1">
                Set up <span class="text-orange-600">your shop</span>
            </h2>
            <p class="text-sm text-gray-500 mb-6">It takes less than a minute.</p>

            <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-5">
                @csrf

                {{-- Step 1 --}}
                <div id="step-1" class="space-y-5 {{ $startStep === 1 ? '' : 'hidden' }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shop name</label>
                        <input type="text" name="name" id="shop-name" value="{{ old('name', auth()->user()->shopname ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="e.g. Shauji General Store">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Business type</label>
                        <select name="business_type"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                            @foreach ($types as $value => $label)
                                <option value="{{ $value }}" @selected(old('business_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('business_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="button" id="next-btn"
                        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 rounded-lg text-sm">
                        Next
                    </button>
                </div>

                {{-- Step 2 --}}
                <div id="step-2" class="space-y-5 {{ $startStep === 2 ? '' : 'hidden' }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shop phone number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="98XXXXXXXX">
                        @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Town, district">
                        @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="button" id="back-btn"
                            class="w-1/3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 rounded-lg text-sm">
                            Back
                        </button>
                        <button type="submit"
                            class="w-2/3 bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 rounded-lg text-sm">
                            Finish setup
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const s1 = document.getElementById('step-1');
        const s2 = document.getElementById('step-2');
        const num = document.getElementById('step-num');
        const nameInput = document.getElementById('shop-name');

        function show(step) {
            s1.classList.toggle('hidden', step !== 1);
            s2.classList.toggle('hidden', step !== 2);
            num.textContent = step;
        }

        document.getElementById('next-btn').addEventListener('click', () => {
            if (!nameInput.value.trim()) { nameInput.focus(); return; }
            show(2);
        });
        document.getElementById('back-btn').addEventListener('click', () => show(1));
    </script>
</x-layout>