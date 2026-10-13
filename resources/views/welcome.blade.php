<x-layout title="Home | ShaujiDotCom">
    @auth
        <!-- Authenticated Dashboard Hero View -->
        <div class="py-16 px-4 flex items-center justify-center min-h-[calc(100vh-80px)] bg-gray-50">
            <div class="w-full max-w-lg bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center">
                
                <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                    Dashboard
                </p>

                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    Welcome, <span class="text-orange-600">{{ auth()->user()->name }}</span>!
                </h2>

                <p class="text-sm text-gray-500 mb-8">
                    Manage your store customers efficiently from your dashboard.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a 
                        href="/create-customer" 
                        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg text-sm transition duration-200 shadow-sm flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        <span>Create Customer</span>
                    </a>

                    <a 
                        href="/customers" 
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg text-sm transition duration-200 flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-users text-xs"></i>
                        <span>List All Customers</span>
                    </a>
                </div>

            </div>
        </div>
    @else
        <!-- Guest View -->
        <main>
            <x-hero />
            <!-- Features Component -->
            <x-features />
            <!-- Stats & Testimonial Component -->
            <x-stats-testimonial />
            <!-- FAQ Component -->
            <x-faq />
            <x-pricing />
        </main>
    @endauth
</x-layout>