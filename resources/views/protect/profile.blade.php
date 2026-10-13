<x-layout title="Profile">
    <div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8 text-gray-800">
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                
                <!-- Avatar / Profile Picture -->
                <div class="relative w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-md bg-orange-100 flex items-center justify-center text-orange-600 text-3xl font-bold">
                    <span id="userInitials">SD</span>
                </div>

                <!-- User Info -->
                <div class="flex-1 text-center sm:text-left">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                {{ Auth::user()->name ?? 'Shauji Owner' }}
                            </h1>
                            <p class="text-sm text-gray-500">
                                {{ Auth::user()->email ?? 'owner@shopname.com' }}
                            </p>
                        </div>

                        <!-- Edit Profile / Action -->
                        <a
                            href="#edit-profile"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition duration-200"
                        >
                            <i class="fa-solid fa-pen text-[10px]"></i>
                            <span>Edit Profile</span>
                        </a>
                    </div>

                    <!-- Shop Tag / Badge -->
                    <div class="mt-4 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 text-orange-700 text-xs font-medium border border-orange-200">
                            <i class="fa-solid fa-store text-[10px]"></i>
                            <span>{{ Auth::user()->shop_name ?? 'Shauji General Store' }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium border border-green-200">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            <span>Active Merchant</span>
                        </span>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total Customers</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">148</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Credit Tracked</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">NPR 45,200</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Member Since</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">Jan 2026</p>
                </div>

            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Account Details</h2>
                    <p class="text-xs text-gray-500">Manage your personal details and shop preferences.</p>
                </div>

                <div class="p-6 space-y-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase">Full Name</p>
                            <p class="text-sm font-medium text-gray-800 mt-0.5">{{ Auth::user()->name ?? 'Shauji Owner' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase">Phone Number</p>
                            <p class="text-sm font-medium text-gray-800 mt-0.5">{{ Auth::user()->phone ?? '+977 98XXXXXXXX' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase">Shop Name</p>
                            <p class="text-sm font-medium text-gray-800 mt-0.5">{{ Auth::user()->shop_name ?? 'Shauji General Store' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase">Location</p>
                            <p class="text-sm font-medium text-gray-800 mt-0.5">Nepal</p>
                        </div>
                    </div>

                    <!-- Action Row -->
                    <div class="pt-2 flex items-center justify-between">
                        <a href="{{ route('forget-password') }}" class="text-xs font-semibold text-orange-600 hover:underline">
                            Change Password?
                        </a>

                        <!-- Logout Button Form -->
                        <form action="{{ route('logout') ?? '#' }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold rounded-lg transition duration-200">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>