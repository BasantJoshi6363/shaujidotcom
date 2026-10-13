<x-layout title="Edit Customer">

    <div class="py-16 px-4 flex items-center justify-center min-h-[calc(100vh-80px)] bg-gray-50">
        <div class="w-full max-w-lg bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            
            <div class="mb-6">
                <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                    Customer Management
                </p>
                <h2 class="text-2xl font-bold text-gray-900">
                    Edit Customer Info
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Update details for <span class="font-semibold text-gray-800">{{ $customer->name }}</span> (ID: #{{ $customer->id }}).
                </p>
            </div>

            <!-- Global Validation Error Alert -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm" role="alert">
                    <div class="flex items-start gap-3">
                        <i class="ri-error-warning-line text-lg mt-0.5"></i>
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

            <form action="{{ route('update-customer', ['id' => $customer->id]) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ri-user-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $customer->name) }}"
                            placeholder="John Doe" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border {{ $errors->has('name') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200"
                        >
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">
                        Email Address
                    </label>
                    <div class="relative">
                        <i class="ri-mail-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', $customer->email) }}"
                            placeholder="customer@example.com" 
                            class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border {{ $errors->has('email') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div>
                    <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">
                        Phone Number
                    </label>
                    <div class="relative">
                        <i class="ri-phone-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone', $customer->phone) }}"
                            placeholder="+977 9800000000" 
                            class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border {{ $errors->has('phone') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200"
                        >
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Field -->
                <div>
                    <label for="address" class="block text-xs font-semibold text-gray-700 mb-1">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ri-map-pin-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input 
                            type="text" 
                            name="address" 
                            id="address" 
                            value="{{ old('address', $customer->address) }}"
                            placeholder="Kathmandu, Nepal" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border {{ $errors->has('address') ? 'border-red-400 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-500' }} outline-none transition duration-200"
                        >
                    </div>
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
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
                        <span>Update Customer</span>
                        <i class="ri-save-line text-base"></i>
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-layout>