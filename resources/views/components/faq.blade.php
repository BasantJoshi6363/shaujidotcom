<section id="faq" class="max-w-4xl mx-auto px-6 py-24">
    <!-- Header -->
    <div class="text-center space-y-3 mb-16">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">Support</span>
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900">
            Frequently Asked Questions
        </h2>
        <p class="text-gray-600 text-base md:text-lg">
            Everything you need to know about setting up and using shauji.com.
        </p>
    </div>

    <!-- Accordion List -->
    <div class="space-y-4" x-data="{ activeAccordion: null }">
        
        <!-- Question 1 -->
        <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden transition">
            <button @click="activeAccordion = activeAccordion === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none">
                <span class="text-base font-bold text-gray-900">Is my data safe on shauji.com?</span>
                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" :class="{ 'rotate-180': activeAccordion === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="activeAccordion === 1" x-collapse class="px-6 pb-5 text-sm text-gray-600 leading-relaxed">
                Yes, your data is completely secure. We use bank-grade 256-bit encryption and back up all financial logs daily so you never lose your records.
            </div>
        </div>

        <!-- Question 2 -->
        <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden transition">
            <button @click="activeAccordion = activeAccordion === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none">
                <span class="text-base font-bold text-gray-900">Do my customers need to install the app too?</span>
                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" :class="{ 'rotate-180': activeAccordion === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="activeAccordion === 2" x-collapse class="px-6 pb-5 text-sm text-gray-600 leading-relaxed">
                No, your customers do not need to download anything. They will receive automated SMS reminders with link summaries directly to their phones.
            </div>
        </div>

        <!-- Question 3 -->
        <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden transition">
            <button @click="activeAccordion = activeAccordion === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none">
                <span class="text-base font-bold text-gray-900">What happens if I lose my phone?</span>
                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" :class="{ 'rotate-180': activeAccordion === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="activeAccordion === 3" x-collapse class="px-6 pb-5 text-sm text-gray-600 leading-relaxed">
                Your data is stored securely in the cloud. Simply log into your account from any new phone or computer, and all your credit ledgers will be right there.
            </div>
        </div>

        <!-- Question 4 -->
        <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden transition">
            <button @click="activeAccordion = activeAccordion === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none">
                <span class="text-base font-bold text-gray-900">Can I manage multiple shops?</span>
                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" :class="{ 'rotate-180': activeAccordion === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="activeAccordion === 4" x-collapse class="px-6 pb-5 text-sm text-gray-600 leading-relaxed">
                Yes, shauji.com allows you to easily switch between different business locations or multiple storefronts under a single user account.
            </div>
        </div>

    </div>

    <!-- Still Have Questions Callout Box -->
    <div class="mt-12 bg-[#111827] rounded-2xl p-8 text-white flex flex-col md:flex-row items-center justify-between shadow-lg space-y-6 md:space-y-0">
        <div class="space-y-1 text-center md:text-left">
            <h3 class="text-lg font-bold">Still have questions?</h3>
            <p class="text-sm text-gray-400">Can’t find the answer you’re looking for? Please chat to our friendly team.</p>
        </div>
        <a href="#contact" class="bg-white text-gray-900 hover:bg-gray-100 transition px-6 py-3 rounded-xl font-medium text-sm shadow-sm whitespace-nowrap">
            Get in touch
        </a>
    </div>
</section>