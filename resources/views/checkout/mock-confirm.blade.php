<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mock Payment Confirmation</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-semibold mb-2">Mock Payment Gateway</h3>
                <p class="text-gray-600 mb-2">This is a simulated payment environment.</p>
                <p class="text-sm text-gray-500 mb-6">No real transaction will occur. Click below to simulate a successful payment.</p>

                <div class="flex gap-4 justify-center">
                    <a href="{{ route('payment.success', ['gateway' => 'mock', 'mock_reference' => $reference]) }}"
                       class="bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600 font-semibold">
                        Simulate Successful Payment
                    </a>
                    <a href="{{ route('payment.cancel', ['gateway' => 'mock', 'mock_reference' => $reference]) }}"
                       class="bg-red-500 text-white px-8 py-3 rounded-lg hover:bg-red-600 font-semibold">
                        Cancel Payment
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
