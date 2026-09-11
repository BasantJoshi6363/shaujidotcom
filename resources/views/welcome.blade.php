<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shaujidotcom</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">

    <!-- Navbar Component -->
    <x-navbar />

    <!-- Hero Component -->
    <x-hero />

    <!-- Features Component -->
    <x-features />

    <!-- Stats & Testimonial Component -->
    <x-stats-testimonial />

    <!-- FAQ Component -->
    <x-faq />
    
    <!-- Footer Component -->
    <x-footer />

</body>

</html>