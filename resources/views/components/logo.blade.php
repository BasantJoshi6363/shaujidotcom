<a href="{{ url('/') }}" class="flex items-center space-x-2 focus:outline-none">
    <div class="relative w-10 h-10 rounded-full overflow-hidden border border-gray-200 flex items-center justify-center bg-white shadow-sm">
        <img
            src="{{ asset('images/logo-96.png') }}"
            srcset="{{ asset('images/mainlogo.png') }} 1x, {{ asset('images/mainlogo.png') }} 2x"
            alt="Shauji Logo"
            class="size-full object-cover"
        >
        <div class="absolute inset-0 bg-black/15 pointer-events-none"></div>
    </div>
    <span class="text-xl font-bold tracking-tight text-gray-900">shauji<span class="text-indigo-600">.com</span></span>
</a>