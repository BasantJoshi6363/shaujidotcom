<x-layout title="Reset Password">
    <div class="py-16 px-4 flex items-center justify-center min-h-[calc(100vh-80px)] bg-gray-50">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                Password recovery
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                Forgot password?
            </h2>

            <p class="text-sm text-gray-500 mb-8">
                Enter your registered email address to receive a verification code.
            </p>

            @if (session('message') || session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-start gap-3" role="alert">
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                    <div>{{ session('message') ?? session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm" role="alert">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <div>
                            <p class="font-semibold mb-1">Request failed</p>
                            <p class="text-xs text-red-600">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="/send-otp" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">
                        Email address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@shopname.com"
                        required
                        class="w-full px-4 py-2.5 text-sm rounded-lg border {{ $errors->has('email') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 rounded-lg text-sm transition duration-200 shadow-sm flex items-center justify-center gap-2"
                >
                    <span>Send Reset Code</span>
                    <i class="fa-solid fa-paper-plane text-xs"></i>
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
</x-layout>