<main class="max-w-7xl mx-auto px-6 md:px-12 py-16 md:py-24">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        <!-- Left Column: Copy & CTAs -->
        <div class="lg:col-span-6 space-y-6 text-left">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-gray-900 leading-[1.15]">
                Manage Shop <span class="text-indigo-600">Credit</span> Effortlessly.
            </h1>
            <p class="text-lg text-gray-600 leading-relaxed max-w-xl">
                Replace your old notebooks with the smartest digital ledger. Track customer credit, send automatic payment reminders, and grow your business with shauji.com.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 pt-2">
                @auth
                    <a href="{{ route('profile') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 text-white font-medium rounded-xl shadow-md hover:bg-indigo-700 transition">
                        Go to Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-[#111827] text-white font-medium rounded-xl shadow-md hover:bg-gray-800 transition">
                        Create Your Free Account &rarr;
                    </a>
                    <a href="#demo" class="inline-flex items-center justify-center px-6 py-3.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl shadow-sm hover:bg-gray-50 transition">
                        Watch Demo
                    </a>
                @endauth
            </div>

            <!-- Social Proof / Users Joined -->
            <div class="flex items-center space-x-4 pt-4">
                <div class="flex -space-x-2 overflow-hidden">
                    <img class="inline-block h-9 w-9 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="User">
                    <img class="inline-block h-9 w-9 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=100&q=80" alt="User">
                    <img class="inline-block h-9 w-9 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=100&q=80" alt="User">
                    <img class="inline-block h-9 w-9 rounded-full ring-2 ring-white object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="User">
                </div>
                <div class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">Joined by 12,000+</span> Shop Owners
                </div>
            </div>
        </div>

        <!-- Right Column: Visual Graphic / Card Preview -->
        <div class="lg:col-span-6 relative">
            <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-gray-100 bg-gray-900 group">
                <!-- Background Shop Image -->
                <img src="https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=1000&q=80" alt="Shop Ledger Dashboard" class="w-full h-[380px] sm:h-[420px] object-cover opacity-85 group-hover:scale-105 transition duration-700">
                
                <!-- Floating Performance Card Overlay -->
                <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-md p-5 rounded-xl shadow-lg border border-white/20 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Live Performance</div>
                        <div class="text-2xl font-black text-gray-900 tracking-tight">NPR 45,820</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 mb-1">
                            Real-time
                        </span>
                        <div class="text-xs font-medium text-emerald-600">+12.5% vs last month</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>