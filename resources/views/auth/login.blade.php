<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | ShaujiDotCom</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon_io/site.webmanifest') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome CDN -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .bg-dark-grid {
            background-color: #121926;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 30px 30px;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 flex flex-col md:flex-row text-gray-800">

    <!-- ===================================================== -->
    <!-- LEFT HERO SECTION -->
    <!-- ===================================================== -->

    <div class="w-full md:w-1/2 bg-dark-grid text-white p-8 md:p-16 flex flex-col justify-between min-h-screen">

        <div>

            <!-- Brand Logo -->
            <div class="flex items-center mb-12">

                <a
                    href="{{ url('/') }}"
                    class="flex items-center space-x-2 focus:outline-none"
                >

                    <div class="relative w-9 h-9 rounded-full overflow-hidden border border-gray-700 flex items-center justify-center bg-white shadow-sm">

                        <img
                            src="{{ asset('images/mainlogo.png') }}"
                            alt="Shauji Logo"
                            class="w-full h-full object-cover scale-110"
                            style="image-rendering: -webkit-optimize-contrast;"
                        >

                        <div class="absolute inset-0 bg-black/15 pointer-events-none"></div>

                    </div>

                    <span class="text-xl font-bold tracking-tight text-white">
                        shauji<span class="text-indigo-500">.com</span>
                    </span>

                </a>

            </div>


            <!-- Hero Heading -->
            <div class="max-w-md mt-10">

                <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-4">
                    Your shop's ledger,<br class="hidden sm:inline">
                    digitized — and always in your pocket.
                </h1>

                <p class="text-gray-400 text-sm md:text-base leading-relaxed">
                    Track customer credit, send automated reminders, and collect faster.
                    Built for shopkeepers across Nepal.
                </p>

            </div>


            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 my-10 max-w-md border-t border-b border-gray-800 py-6">

                <div>
                    <p class="text-xl md:text-2xl font-bold text-white">
                        12,000+
                    </p>

                    <p class="text-xs text-gray-400">
                        Shop owners
                    </p>
                </div>


                <div class="border-l border-gray-800 pl-4">

                    <p class="text-xl md:text-2xl font-bold text-white">
                        NPR 1.2B+
                    </p>

                    <p class="text-xs text-gray-400">
                        Credit tracked
                    </p>

                </div>


                <div class="border-l border-gray-800 pl-4">

                    <p class="text-xl md:text-2xl font-bold text-white">
                        98%
                    </p>

                    <p class="text-xs text-gray-400">
                        Retention
                    </p>

                </div>

            </div>

        </div>


        <!-- Testimonial -->
        <div class="bg-[#1A2232] bg-opacity-80 border border-gray-800 rounded-xl p-5 max-w-md backdrop-blur-sm">

            <p class="text-sm text-gray-300 italic">
                "I no longer spend hours every Sunday figuring out who owes me money.
                The reminders do the work for me."
            </p>

        </div>

    </div>


    <!-- ===================================================== -->
    <!-- RIGHT LOGIN SECTION -->
    <!-- ===================================================== -->

    <div class="w-full md:w-1/2 bg-white p-8 md:p-16 flex items-center justify-center min-h-screen">

        <div class="w-full max-w-md">

            <!-- Header -->
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase mb-1">
                Welcome back
            </p>

            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                Log in to your shop
            </h2>

            <p class="text-sm text-gray-500 mb-8">
                Enter your details to access your dashboard.
            </p>


            <!-- ================================================= -->
            <!-- SUCCESS MESSAGE -->
            <!-- ================================================= -->

            @if (session('success'))

                <div
                    class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-start gap-3"
                    role="alert"
                >

                    <i class="fa-solid fa-circle-check mt-0.5"></i>

                    <div>
                        {{ session('success') }}
                    </div>

                </div>

            @endif


            <!-- ================================================= -->
            <!-- ERROR MESSAGE -->
            <!-- ================================================= -->

            @if ($errors->any())

                <div
                    class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"
                    role="alert"
                >

                    <div class="flex items-start gap-3">

                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

                        <div>

                            <p class="font-semibold mb-1">
                                Login failed
                            </p>

                            <p class="text-xs text-red-600">
                                {{ $errors->first() }}
                            </p>

                        </div>

                    </div>

                </div>

            @endif


            <!-- ================================================= -->
            <!-- LOGIN FORM -->
            <!-- ================================================= -->

            <form
                action="{{ route('login.post') }}"
                method="POST"
                class="space-y-5"
            >

                @csrf


                <!-- Email / Phone -->
                <div>

                    <label
                        for="email"
                        class="block text-xs font-semibold text-gray-700 mb-1"
                    >
                        Email or phone number
                    </label>

                    <input
                        type="text"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@shopname.com"
                        autocomplete="username"
                        required

                        class="w-full px-4 py-2.5 text-sm rounded-lg border
                        {{ $errors->has('email')
                            ? 'border-red-400 focus:ring-red-500 focus:border-red-500'
                            : 'border-gray-300 focus:ring-orange-500 focus:border-orange-500'
                        }}
                        outline-none transition duration-200"
                    >

                    @error('email')

                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                <!-- Password -->
                <div>

                    <label
                        for="password"
                        class="block text-xs font-semibold text-gray-700 mb-1"
                    >
                        Password
                    </label>

                    <div class="relative">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required

                            class="w-full px-4 py-2.5 text-sm rounded-lg border
                            {{ $errors->has('password')
                                ? 'border-red-400 focus:ring-red-500 focus:border-red-500'
                                : 'border-gray-300 focus:ring-orange-500 focus:border-orange-500'
                            }}
                            outline-none transition duration-200 pr-16"
                        >

                        <button
                            type="button"
                            onclick="togglePassword()"
                            id="passwordToggle"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400 hover:text-gray-600"
                        >
                            Show
                        </button>

                    </div>

                    @error('password')

                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                <!-- ================================================= -->
                <!-- REMEMBER / FORGOT PASSWORD -->
                <!-- ================================================= -->

                <div class="flex items-center justify-between text-xs">

                    <label class="flex items-center space-x-2 cursor-pointer text-gray-600">

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                        >

                        <span>
                            Remember me
                        </span>

                    </label>


                    <a
                        href="{{ route('forget-password') }}"
                        class="font-semibold text-gray-800 hover:text-orange-600 transition"
                    >
                        Forgot password?
                    </a>

                </div>


                <!-- ================================================= -->
                <!-- LOGIN BUTTON -->
                <!-- ================================================= -->

                <button
                    type="submit"
                    class="w-full bg-orange-600 hover:bg-orange-700
                    text-white font-medium py-3 rounded-lg text-sm
                    transition duration-200 shadow-sm"
                >
                    Log in
                </button>

            </form>


            <!-- ================================================= -->
            <!-- SOCIAL DIVIDER -->
            <!-- ================================================= -->

            <div class="relative my-6 text-center">

                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>

                <span class="relative bg-white px-3 text-xs text-gray-400 uppercase tracking-wider">
                    or continue with
                </span>

            </div>


            <!-- ================================================= -->
            <!-- SOCIAL LOGIN BUTTONS -->
            <!-- ================================================= -->

            <div class="space-y-3">

                <!-- Google -->
                <a
                    href="{{ route('google.redirect') }}"
                    class="w-full flex items-center justify-center gap-2
                    border border-gray-300 rounded-lg py-2.5
                    text-sm font-medium text-gray-700
                    hover:bg-gray-50 hover:border-gray-400
                    transition duration-200"
                >

                    <img
                        src="{{ asset('images/social/google.png') }}"
                        alt="Google"
                        class="w-5 h-5 object-contain"
                    >

                    <span>
                        Continue with Google
                    </span>

                </a>


                <!-- Facebook -->
                <a
                    href="{{ route('facebook.redirect') }}"
                    class="w-full flex items-center justify-center gap-2
                    border border-gray-300 rounded-lg py-2.5
                    text-sm font-medium text-gray-700
                    hover:bg-gray-50 hover:border-gray-400
                    transition duration-200"
                >

                    <img
                        src="{{ asset('images/social/facebook.png') }}"
                        alt="Facebook"
                        class="w-5 h-5 object-contain"
                    >

                    <span>
                        Continue with Facebook
                    </span>

                </a>

            </div>


            <!-- ================================================= -->
            <!-- REGISTER LINK -->
            <!-- ================================================= -->

            <p class="text-center text-xs text-gray-500 mt-8">

                New to ShaujiDotCom?

                <a
                    href="{{ route('register') }}"
                    class="text-orange-600 font-semibold hover:underline"
                >
                    Create a free account
                </a>

            </p>

        </div>

    </div>


    <!-- ===================================================== -->
    <!-- PASSWORD TOGGLE -->
    <!-- ===================================================== -->

    <script>
        function togglePassword() {

            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');

            if (passwordInput.type === 'password') {

                passwordInput.type = 'text';
                passwordToggle.textContent = 'Hide';

            } else {

                passwordInput.type = 'password';
                passwordToggle.textContent = 'Show';

            }
        }
    </script>

</body>
</html>
