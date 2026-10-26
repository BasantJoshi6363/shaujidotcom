<x-layout title="Verify Email">
    <div class="py-16 px-4 flex items-center justify-center min-h-[calc(100vh-80px)] bg-gray-50">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center">

            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                Verify your email
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                Enter the <span class="text-orange-600">6-digit code</span>
            </h2>

            <p class="text-sm text-gray-500 mb-6">
                We sent a code to <strong>{{ $email }}</strong>. It expires in 10 minutes.
            </p>

            @if (session('status'))
                <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-100 rounded-lg py-2 px-3">
                    {{ session('status') }}
                </div>
            @endif

            @error('otp')
                <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-100 rounded-lg py-2 px-3">
                    {{ $message }}
                </div>
            @enderror

            <form method="POST" action="{{ route('verification.verify') }}" class="space-y-4">
                @csrf
                <input
                    type="text"
                    name="otp"
                    inputmode="numeric"
                    maxlength="6"
                    autocomplete="one-time-code"
                    autofocus
                    placeholder="000000"
                    class="w-full text-center text-2xl tracking-[0.5em] py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                >
                <button type="submit"
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg text-sm transition duration-200 shadow-sm">
                    Verify
                </button>
            </form>

            <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                    Send a new code
                </button>
            </form>

        </div>
    </div>
</x-layout>