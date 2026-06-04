<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order History</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            @if($orders->isEmpty())
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-500 text-lg">No orders yet.</p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-4 bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600">Start Shopping</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">Order #{{ $order->id }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 rounded text-sm
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <p class="text-lg font-bold mt-2">Tsh {{ number_format($order->total_price, 0) }}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 text-sm hover:underline">View Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
