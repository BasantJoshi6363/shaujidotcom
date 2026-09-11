<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ShaujiDotCom</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .bg-dark-grid {
            background-color: #121926;
            background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 30px 30px;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col md:flex-row text-gray-800">

    <!-- Left Hero Section -->
    <div class="w-full md:w-1/2 bg-dark-grid text-white p-8 md:p-16 flex flex-col justify-between min-h-screen">
        <div>
            <!-- Brand Logo / Header -->
            <a href="{{ url('/') }}" class="flex items-center space-x-2 focus:outline-none">
                    <div class="relative w-9 h-9 rounded-full overflow-hidden border border-gray-700 flex items-center justify-center bg-white shadow-sm">
                        <img src="{{ asset('images/mainlogo.jpg') }}" alt="Shauji Logo" class="w-full h-full object-cover scale-110" style="image-rendering: -webkit-optimize-contrast;">
                        <div class="absolute inset-0 bg-black/15 pointer-events-none"></div>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">shauji<span class="text-indigo-500">.com</span></span>
                </a>

            <!-- Hero Heading -->
            <div class="max-w-md mt-6">
                <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-4">
                    Your shop's ledger,<br class="hidden sm:inline"> digitized — and always in your pocket.
                </h1>
                <p class="text-gray-400 text-sm md:text-base leading-relaxed">
                    Track customer credit, send automated reminders, and collect faster. Built for shopkeepers across Nepal.
                </p>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-3 gap-4 my-8 max-w-md border-t border-b border-gray-800 py-6">
                <div>
                    <p class="text-xl md:text-2xl font-bold text-white">12,000+</p>
                    <p class="text-xs text-gray-400">Shop owners</p>
                </div>
                <div class="border-l border-gray-800 pl-4">
                    <p class="text-xl md:text-2xl font-bold text-white">NPR 1.2B+</p>
                    <p class="text-xs text-gray-400">Credit tracked</p>
                </div>
                <div class="border-l border-gray-800 pl-4">
                    <p class="text-xl md:text-2xl font-bold text-white">98%</p>
                    <p class="text-xs text-gray-400">Retention</p>
                </div>
            </div>
        </div>

        <!-- Testimonial Card -->
        <div class="bg-[#1A2232] bg-opacity-80 border border-gray-800 rounded-xl p-5 max-w-md backdrop-blur-sm my-6 md:my-0">
            <p class="text-sm text-gray-300 italic mb-4">
                "I no longer spend hours every Sunday figuring out who owes me money. The reminders do the work for me."
            </p>
            
        </div>
    </div>

    <!-- Right Registration Form Section -->
    <div class="w-full md:w-1/2 bg-white p-8 md:p-16 flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md py-6">
            
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">Get started for free</p>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Create your account</h2>
            <p class="text-sm text-gray-500 mb-6">Enter your details to register your shop dashboard.</p>

            <!-- Error Banner -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="post" class="space-y-4">
                @csrf
                
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" required 
                        class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition duration-200">
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@shopname.com" required 
                        class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition duration-200">
                </div>

                <!-- Shop Name & Phone (Grid Layout) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="shopname" class="block text-xs font-semibold text-gray-700 mb-1">Shop Name</label>
                        <input type="text" id="shopname" name="shopname" value="{{ old('shopname') }}" placeholder="Kirana Store" required 
                            class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition duration-200">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="98XXXXXXXX" required 
                            class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition duration-200">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required 
                        class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition duration-200">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required 
                        class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition duration-200">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 rounded-lg text-sm transition duration-200 shadow-sm mt-2">
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <p class="text-center text-xs text-gray-500 mt-6">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-orange-600 font-semibold hover:underline">Log in here</a>
            </p>

        </div>
    </div>

</body>
</html>