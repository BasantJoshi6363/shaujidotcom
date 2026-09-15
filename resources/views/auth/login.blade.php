<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ShaujiDotCom</title>
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
            <div class="flex items-center space-x-3 mb-12">

                <a href="{{ url('/') }}" class="flex items-center space-x-2 focus:outline-none">
                    <div class="relative w-9 h-9 rounded-full overflow-hidden border border-gray-700 flex items-center justify-center bg-white shadow-sm">
                        <img src="{{ asset('images/mainlogo.png') }}" alt="Shauji Logo" class="w-full h-full object-cover scale-110" style="image-rendering: -webkit-optimize-contrast;">
                        <div class="absolute inset-0 bg-black/15 pointer-events-none"></div>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">shauji<span class="text-indigo-500">.com</span></span>
                </a>
            </div>

            <!-- Hero Heading -->
            <div class="max-w-md mt-10">
                <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-4">
                    Your shop's ledger,<br class="hidden sm:inline"> digitized — and always in your pocket.
                </h1>
                <p class="text-gray-400 text-sm md:text-base leading-relaxed">
                    Track customer credit, send automated reminders, and collect faster. Built for shopkeepers across Nepal.
                </p>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-3 gap-4 my-10 max-w-md border-t border-b border-gray-800 py-6">
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
        <div class="bg-[#1A2232] bg-opacity-80 border border-gray-800 rounded-xl p-5 max-w-md backdrop-blur-sm">
            <p class="text-sm text-gray-300 italic mb-4">
                "I no longer spend hours every Sunday figuring out who owes me money. The reminders do the work for me."
            </p>
        </div>
    </div>

    <!-- Right Login Form Section -->
    <div class="w-full md:w-1/2 bg-white p-8 md:p-16 flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md">
            
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">Welcome back</p>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Log in to your shop</h2>
            <p class="text-sm text-gray-500 mb-8">Enter your details to access your dashboard.</p>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Email/Phone Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email or phone number</label>
                    <input type="email" id="email" name="email" placeholder="you@shopname.com" required 
                        class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition duration-200">
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required 
                            class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition duration-200 pr-16">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400 hover:text-gray-600">
                            Show
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 cursor-pointer text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span>Remember me</span>
                    </label>
                    <a href="{{ route('forget-password') }}" class="font-semibold text-gray-800 hover:text-orange-600">Forgot password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 rounded-lg text-sm transition duration-200 shadow-sm">
                    Log in
                </button>
            </form>

            <!-- Social Divider -->
            <div class="relative my-6 text-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <span class="relative bg-white px-3 text-xs text-gray-400 uppercase tracking-wider">or continue with</span>
            </div>

            <!-- Social Logins -->
            <div class="space-y-3">
                <a href="{{ route('google.redirect') }}" 
                    class="w-full flex items-center justify-center space-x-2 border border-gray-300 rounded-lg py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.15C3.26 21.3 7.33 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.24C.45 8.15 0 9.99 0 12s.45 3.85 1.24 5.42l4.04-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.7 1.24 6.58l4.04 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Google</span>
                </a>

                <a href="{{ route('facebook.redirect') }}" 
                    class="w-full flex items-center justify-center space-x-2 border border-gray-300 rounded-lg py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                    <i class="fa-brands fa-facebook text-blue-600 text-base"></i>
                    <span>Facebook</span>
                </a>
            </div>

            <!-- Register Link -->
            <p class="text-center text-xs text-gray-500 mt-8">
                New to ShaujiDotCom? 
                <a href="/register" class="text-orange-600 font-semibold hover:underline">Create a free account</a>
            </p>

        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        }
    </script>
</body>
</html>