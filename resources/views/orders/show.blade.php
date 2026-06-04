<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Order Date: {{ $order->created_at->format('M d, Y h:i A') }}</p>
                        <p class="text-sm text-gray-500">Status:
                            <span class="px-3 py-1 rounded text-sm
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>
                    </div>
                    <p class="text-2xl font-bold">Tsh {{ number_format($order->total_price, 0) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Product</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Price (Tsh)</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Quantity</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $item->product->name }}</td>
                                <td class="px-6 py-4 text-sm">Tsh {{ number_format($item->price, 0) }}</td>
                                <td class="px-6 py-4 text-sm">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-sm font-semibold">Tsh {{ number_format($item->price * $item->quantity, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-between align-items-center">
                <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:underline">&larr; Back to Orders</a>
                <a href="{{ route('receipt.show', $order) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2 1.5A.5.5 0 0 1 2.5 1h11a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5zM1 12.5A1.5 1.5 0 0 0 2.5 14h11a1.5 1.5 0 0 0 1.5-1.5V7a1 1 0 0 0-1-1h-1a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-.5.5h-6a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-.5-.5h-1a1 1 0 0 0-1 1z"/></svg>
                    View Receipt
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
