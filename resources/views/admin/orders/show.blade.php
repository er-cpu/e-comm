<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">{{ session('success') }}<button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Customer: <strong>{{ $order->user->first_name }} {{ $order->user->last_name }}</strong></p>
                        <p class="text-sm text-gray-500">Email: {{ $order->user->email }}</p>
                        <p class="text-sm text-gray-500">Date: {{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <p class="text-2xl font-bold">Tsh {{ number_format($order->total_price, 0) }}</p>
                </div>

                <div class="mt-4">
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex items-center gap-4">
                        @csrf
                        @method('PATCH')
                        <label class="text-sm font-medium">Update Status:</label>
                        <select name="status" class="rounded border-gray-300">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded text-sm hover:bg-indigo-600">Update</button>
                    </form>
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

            <div class="mt-4">
                <a href="{{ route('admin.orders') }}" class="text-indigo-600 hover:underline">&larr; Back to Orders</a>
            </div>
        </div>
    </div>
</x-app-layout>
