<x-layout title="Settings">
    <div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8 text-gray-800">
        <div class="max-w-4xl mx-auto space-y-6">

            <!-- Page Title -->
            <div>
                <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                    Preferences
                </p>
                <h1 class="text-2xl font-bold text-gray-900">
                    Account Settings
                </h1>
                <p class="text-sm text-gray-500">
                    Manage your shop profile, automated SMS reminders, and security options.
                </p>
            </div>

            <!-- Flash Alert (Success) -->
            @if (session('success'))
                <div class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-start gap-3" role="alert">
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <!-- Main Content Container -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- LEFT SIDEBAR / NAVIGATION -->
                <div class="space-y-1">
                    <button type="button" onclick="switchTab('general')" id="tab-btn-general" class="tab-btn active w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold text-left transition duration-200 bg-white shadow-sm text-orange-600 border-l-4 border-orange-600">
                        <i class="fa-solid fa-store w-5 text-center"></i>
                        <span>Shop Profile</span>
                    </button>

                    <button type="button" onclick="switchTab('reminders')" id="tab-btn-reminders" class="tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold text-left transition duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                        <i class="fa-solid fa-bell w-5 text-center"></i>
                        <span>SMS Reminders (For Premium Users)</span>
                    </button>

                    <button type="button" onclick="switchTab('security')" id="tab-btn-security" class="tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold text-left transition duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                        <i class="fa-solid fa-lock w-5 text-center"></i>
                        <span>Security</span>
                    </button>

                    <button type="button" onclick="switchTab('danger')" id="tab-btn-danger" class="tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold text-left transition duration-200 text-red-600 hover:bg-red-50">
                        <i class="fa-solid fa-triangle-exclamation w-5 text-center"></i>
                        <span>Danger Zone</span>
                    </button>
                </div>

                <!-- RIGHT CONTENT PANEL -->
                <div class="md:col-span-2">
                    
                    <!-- TAB 1: SHOP PROFILE -->
                    <div id="tab-content-general" class="tab-panel bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Shop Details</h2>
                            <p class="text-xs text-gray-500">Update your store identity shown to customers.</p>
                        </div>

                        <form action="/settings/profile" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="shop_name" class="block text-xs font-semibold text-gray-700 mb-1">Shop Name</label>
                                <input type="text" id="shop_name" name="shop_name" value="{{ Auth::user()->shop_name ?? 'Shauji General Store' }}" required class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                            </div>

                            <div>
                                <label for="owner_name" class="block text-xs font-semibold text-gray-700 mb-1">Owner Full Name</label>
                                <input type="text" id="owner_name" name="name" value="{{ Auth::user()->name ?? 'Shauji Owner' }}" required class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="{{ Auth::user()->phone ?? '+977 9800000000' }}" required class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                            </div>

                            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm transition shadow-sm">
                                Save Profile
                            </button>
                        </form>
                    </div>

                    <!-- TAB 2: REMINDERS -->
                    <div id="tab-content-reminders" class="tab-panel hidden bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Automated Credit Reminders</h2>
                            <p class="text-xs text-gray-500">Configure automated customer notifications.</p>
                        </div>

                        <form action="/settings/reminders" method="POST" class="space-y-5">
                            @csrf
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Auto SMS Reminders</p>
                                    <p class="text-xs text-gray-500">Send automatic SMS to overdue customers</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="auto_sms" value="1" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                                </label>
                            </div>

                            <div>
                                <label for="reminder_days" class="block text-xs font-semibold text-gray-700 mb-1">Send Reminder Frequency</label>
                                <select id="reminder_days" name="reminder_days" class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-orange-500 outline-none bg-white">
                                    <option value="7">Every 7 Days</option>
                                    <option value="15" selected>Every 15 Days</option>
                                    <option value="30">Monthly</option>
                                </select>
                            </div>

                            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm transition shadow-sm">
                                Save Preferences
                            </button>
                        </form>
                    </div>

                    <!-- TAB 3: SECURITY -->
                    <div id="tab-content-security" class="tab-panel hidden bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Security & Password</h2>
                            <p class="text-xs text-gray-500">Keep your store account safe.</p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Password Recovery</p>
                                <p class="text-xs text-gray-500">Reset your login credentials safely</p>
                            </div>
                            <a href="{{ route('forget-password') }}" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg transition">
                                Change Password
                            </a>
                        </div>
                    </div>

                    <!-- TAB 4: DANGER ZONE -->
                    <div id="tab-content-danger" class="tab-panel hidden bg-white p-6 rounded-2xl border border-red-100 shadow-sm space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-red-600">Danger Zone</h2>
                            <p class="text-xs text-gray-500">Irreversible account actions.</p>
                        </div>

                        <div class="p-4 bg-red-50 rounded-xl border border-red-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-red-800">Delete Ledger Account</p>
                                <p class="text-xs text-red-600">Permanently remove all recorded shop transactions and customers.</p>
                            </div>
                            <button type="button" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition">
                                Delete Account
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- TAB SWITCHING SCRIPT -->
    <script>
        function switchTab(tabName) {
            // Hide all panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.add('hidden');
            });

            // Reset tab styles
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = "tab-btn w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold text-left transition duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900";
            });

            // Show selected panel
            document.getElementById('tab-content-' + tabName).classList.remove('hidden');

            // Set active tab style
            const activeBtn = document.getElementById('tab-btn-' + tabName);
            if (tabName === 'danger') {
                activeBtn.className = "tab-btn active w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold text-left transition duration-200 bg-red-50 text-red-600 border-l-4 border-red-600";
            } else {
                activeBtn.className = "tab-btn active w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold text-left transition duration-200 bg-white shadow-sm text-orange-600 border-l-4 border-orange-600";
            }
        }
    </script>
</x-layout>