<x-layout title="Customers">
    <!-- Remix Icon CDN -->

    <div class="py-12 px-4 max-w-6xl mx-auto min-h-[calc(100vh-80px)]">
        
        <!-- Header & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                    Management
                </p>
                <h1 class="text-2xl font-bold text-gray-900">
                    Customer Directory
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    View, search, edit, or delete your registered store customers.
                </p>
            </div>

            <div>
                <a 
                    href="/create-customer" 
                    class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 px-5 rounded-lg text-sm transition duration-200 shadow-sm inline-flex items-center gap-2"
                >
                    <i class="ri-user-add-line text-base"></i>
                    <span>Add New Customer</span>
                </a>
            </div>
        </div>

        <!-- Success Flash Alert -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-start gap-3" role="alert">
                <i class="ri-checkbox-circle-line text-lg mt-0.5"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- Search Bar Form -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <form action="/search-customers" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-grow">
                    <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <input 
                        type="text" 
                        name="search-customer" 
                        value="{{ request('search-customer') }}"
                        placeholder="Search by customer name or email..." 
                        required
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition duration-200"
                    >
                </div>
                
                <div class="flex gap-2">
                    <button 
                        type="submit" 
                        class="bg-gray-900 hover:bg-gray-800 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition duration-200 flex items-center justify-center gap-2"
                    >
                        <span>Search</span>
                    </button>

                    @if(request('search-customer'))
                        <a 
                            href="/customers" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium px-4 py-2.5 rounded-lg text-sm transition duration-200 flex items-center justify-center"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Customer Table Layout -->
        @if($customer->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">ID</th>
                                <th class="py-3.5 px-6">Customer Name</th>
                                <th class="py-3.5 px-6">Contact Info</th>
                                <th class="py-3.5 px-6">Address</th>
                                <th class="py-3.5 px-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach ($customer as $customers)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6 whitespace-nowrap font-medium text-gray-400">
                                        #{{ $customers->id }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $customers->name }}</div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="space-y-1 text-xs">
                                            <div class="text-gray-800 flex items-center gap-1.5">
                                                <i class="ri-mail-line text-gray-400 text-sm"></i>
                                                {{ $customers->email ?? 'N/A' }}
                                            </div>
                                            <div class="text-gray-500 flex items-center gap-1.5">
                                                <i class="ri-phone-line text-gray-400 text-sm"></i>
                                                {{ $customers->phone ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                            <i class="ri-map-pin-line text-gray-400 text-sm flex-shrink-0"></i>
                                            <span class="max-w-xs truncate">{{ $customers->address ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Edit Button -->
                                            <a 
                                                href="/edit/{{ $customers->id }}" 
                                                class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium rounded-lg text-xs transition duration-200 inline-flex items-center gap-1.5"
                                            >
                                                <i class="ri-pencil-line text-sm"></i>
                                                <span>Edit</span>
                                            </a>

                                            <!-- Delete Button -->
                                            <form action="{{ route('delete-customer', ['id' => $customers->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-lg text-xs transition duration-200 inline-flex items-center gap-1.5"
                                                >
                                                    <i class="ri-delete-bin-line text-sm"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
                <i class="ri-user-unfollow-line text-4xl text-gray-300 mb-3 block"></i>
                <h3 class="text-base font-semibold text-gray-800">No Customers Found</h3>
                <p class="text-sm text-gray-500 mt-1 mb-6">There are no customers matching your query or added yet.</p>
                <a 
                    href="/create-customer" 
                    class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition duration-200 inline-flex items-center gap-2"
                >
                    <i class="ri-add-line text-base"></i>
                    <span>Create One Now</span>
                </a>
            </div>
        @endif

    </div>
</x-layout>