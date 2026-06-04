<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trust Policy</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8">
                <h1 class="text-3xl font-bold mb-6">Trust Policy</h1>

                <p class="text-gray-700 mb-4">
                    SmartTrade Secure Platform uses a trust management system to evaluate user behavior and secure transactions.
                </p>

                <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                    <li>Biometric verification increases trust score</li>
                    <li>Trusted devices are marked as secure</li>
                    <li>Failed logins reduce trust level</li>
                    <li>Successful payments increase trust level</li>
                </ul>

                <h3 class="text-xl font-semibold mb-2">Trust Levels</h3>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li>Low Trust (0–30)</li>
                    <li>Medium Trust (31–70)</li>
                    <li>High Trust (71–100)</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
