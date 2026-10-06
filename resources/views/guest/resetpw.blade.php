<x-layout title="Reset Password | ShaujiDotCom">
    <div class="py-16 px-4 flex items-center justify-center min-h-[calc(100vh-80px)] bg-gray-50 text-gray-800">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-gray-100">

            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                Account security
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                Set new password
            </h2>

            <p class="text-sm text-gray-500 mb-8">
                Please enter your new password below.
            </p>

            @if (session('status') || session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-start gap-3" role="alert">
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                    <div>{{ session('status') ?? session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm" role="alert">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <div>
                            <p class="font-semibold mb-1">Reset failed</p>
                            <p class="text-xs text-red-600">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="/reset-password" method="POST" class="space-y-5">
                @csrf

                <!-- New Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">
                        New Password
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter new password"
                            autocomplete="new-password"
                            required
                            class="w-full px-4 py-2.5 text-sm rounded-lg border {{ $errors->has('password') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200 pr-16"
                        >
                        <button
                            type="button"
                            onclick="togglePasswordVisibility('password', 'passwordToggle')"
                            id="passwordToggle"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400 hover:text-gray-600"
                        >
                            Show
                        </button>
                    </div>

                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1">
                        Confirm Password
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Confirm new password"
                            autocomplete="new-password"
                            required
                            class="w-full px-4 py-2.5 text-sm rounded-lg border {{ $errors->has('password_confirmation') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200 pr-16"
                        >
                        <button
                            type="button"
                            onclick="togglePasswordVisibility('password_confirmation', 'confirmPasswordToggle')"
                            id="confirmPasswordToggle"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400 hover:text-gray-600"
                        >
                            Show
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 rounded-lg text-sm transition duration-200 shadow-sm flex items-center justify-center gap-2"
                >
                    <span>Reset Password</span>
                    <i class="fa-solid fa-key text-xs"></i>
                </button>
            </form>

            <p class="text-center text-xs text-gray-500 mt-8">
                Remembered your password?
                <a href="{{ route('login') }}" class="text-orange-600 font-semibold hover:underline">
                    Back to login
                </a>
            </p>

        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, toggleBtnId) {
            const input = document.getElementById(inputId);
            const toggleBtn = document.getElementById(toggleBtnId);

            if (input.type === 'password') {
                input.type = 'text';
                toggleBtn.textContent = 'Hide';
            } else {
                input.type = 'password';
                toggleBtn.textContent = 'Show';
            }
        }
    </script>
</x-layout>