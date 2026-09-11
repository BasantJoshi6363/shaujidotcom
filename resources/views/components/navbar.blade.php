<nav class="w-full bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto py-4 px-6 md:px-12 flex items-center justify-between">
        <!-- Brand Logo & Name -->
        <x-logo />

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-8 text-sm font-medium text-gray-600">
            <a href="#features" class="hover:text-gray-900 transition-colors">Features</a>
            <a href="#how-it-works" class="hover:text-gray-900 transition-colors">How it Works</a>
            <a href="#pricing" class="hover:text-gray-900 transition-colors">Pricing</a>
            <a href="#faq" class="hover:text-gray-900 transition-colors">FAQ</a>
        </div>

        <!-- Right Actions: Conditional Auth Check -->
        <div class="flex items-center space-x-4">
            @auth
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">Profile</a>
                    <a href="{{ route('settings') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">Setting</a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800 transition-colors">Logout</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors px-3 py-2">
                    Login
                </a>
                <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-[#111827] hover:bg-gray-800 transition-all px-5 py-2.5 rounded-xl shadow-sm">
                    Get Started Free
                </a>
            @endauth
        </div>
    </div>
</nav>