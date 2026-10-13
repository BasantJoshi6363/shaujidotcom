<x-layout title="Create Customer">
    <div class="py-16 px-4 flex items-center justify-center min-h-[calc(100vh-80px)] bg-gray-50">
        <div class="w-full max-w-lg bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            
            <div class="mb-6">
                <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                    Customer Management
                </p>
                <h2 class="text-2xl font-bold text-gray-900">
                    Create New Customer
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Fill in the details below to add a new customer to your database.
                </p>
            </div>

            <!-- Global Validation Error Alert -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm" role="alert">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <div>
                            <p class="font-semibold mb-1">Please fix the errors below:</p>
                            <ul class="list-disc list-inside text-xs text-red-600 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="/create-customer" method="POST" class="space-y-4">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}"
                        placeholder="Hari Bahadur Sigdel" 
                        required 
                        class="w-full px-4 py-2.5 text-sm rounded-lg border {{ $errors->has('name') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}"
                        placeholder="customer@example.com" 
                        class="w-full px-4 py-2.5 text-sm rounded-lg border {{ $errors->has('email') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div>
                    <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">
                        Phone Number
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone') }}"
                        placeholder="+977 9800000000" 
                        class="w-full px-4 py-2.5 text-sm rounded-lg border {{ $errors->has('phone') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200"
                    >
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Field -->
                <div>
                    <label for="address" class="block text-xs font-semibold text-gray-700 mb-1">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="address" 
                        id="address" 
                        value="{{ old('address') }}"
                        placeholder="Kathmandu, Nepal" 
                        required 
                        class="w-full px-4 py-2.5 text-sm rounded-lg border {{ $errors->has('address') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200"
                    >
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons Section -->
                <div class="pt-4 flex items-center justify-between gap-4">
                    <a 
                        href="/customers" 
                        class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 rounded-lg text-sm transition duration-200 text-center"
                    >
                        Cancel
                    </a>

                    <button 
                        type="submit" 
                        class="w-1/2 bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 rounded-lg text-sm transition duration-200 shadow-sm flex items-center justify-center gap-2"
                    >
                        <span>Create Customer</span>
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-layout>