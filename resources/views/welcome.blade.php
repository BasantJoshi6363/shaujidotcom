<x-layout>
   @auth
   welcome {{ auth()->user()->name }}
   @else
        <x-hero />
        <!-- Features Component -->
        <x-features />
        <!-- Stats & Testimonial Component -->
        <x-stats-testimonial />
        <!-- FAQ Component -->
        <x-faq />
        <x-pricing />
    </main> "
    @endauth

</x-layout>