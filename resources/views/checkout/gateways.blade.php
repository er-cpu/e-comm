<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Choose Payment Method</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Select a payment method</h3>
                </div>

                <div class="p-6 space-y-4">
                    @foreach($gateways as $name => $gateway)
                        <form action="{{ route('checkout.process') }}" method="POST">
                            @csrf
                            <input type="hidden" name="gateway" value="{{ $name }}">
                            <button type="submit" class="w-full flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition text-left">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-lg">
                                        {{ strtoupper(substr($gateway->getDisplayName(), 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $gateway->getDisplayName() }}</p>
                                        <p class="text-sm text-gray-500">
                                            @if($name === 'stripe') Pay with credit or debit card via Stripe
                                            @elseif($name === 'paypal') Pay with your PayPal account
                                            @elseif($name === 'flutterwave') Pay via Flutterwave (Cards, Mobile Money)
                                            @else Test payment - no real charge
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </form>
                    @endforeach
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('cart.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Cart</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
