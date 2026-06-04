<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Privacy Policy</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8">
                <h1 class="text-3xl font-bold mb-6">Privacy Policy</h1>

                <p class="text-gray-700 mb-4">
                    SmartTrade Secure Platform respects user privacy and ensures that all personal data is protected using secure encryption and authentication systems.
                </p>

                <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li>User passwords are hashed using bcrypt</li>
                    <li>No payment data is stored in the system</li>
                    <li>Personal data is used only for authentication and order processing</li>
                    <li>No data is shared with third parties</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
