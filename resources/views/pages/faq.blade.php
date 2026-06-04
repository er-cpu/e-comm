<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Frequently Asked Questions</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8">
                <h1 class="text-3xl font-bold mb-6">Frequently Asked Questions</h1>

                <div class="space-y-6">
                    <div>
                        <h4 class="font-semibold text-lg">How do I place an order?</h4>
                        <p class="text-gray-700">Add products to cart and proceed to checkout.</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-lg">How does fingerprint authentication work?</h4>
                        <p class="text-gray-700">The system simulates biometric verification or uses OTP fallback for unsupported devices.</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-lg">What happens if OTP fails?</h4>
                        <p class="text-gray-700">You can request a new OTP after a short delay.</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-lg">Is my payment secure?</h4>
                        <p class="text-gray-700">Yes, all payments are simulated and protected by trust validation rules.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
