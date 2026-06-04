<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Products</h2>
            <a href="{{ route('admin.products.create') }}" class="bg-indigo-500 text-white px-4 py-2 rounded text-sm hover:bg-indigo-600">Add Product</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">{{ session('success') }}<button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Category</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Price (Tsh)</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Discount</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Stock</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $product->name }}</td>
                                <td class="px-6 py-4 text-sm">{{ $product->category?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">Tsh {{ number_format($product->price, 0) }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $product->discount_percent > 0 ? $product->discount_percent.'%' : '-' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $product->stock }}</td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
