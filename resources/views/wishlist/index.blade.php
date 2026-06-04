<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Wishlist</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
            @endif

            @if($wishlistItems->isEmpty())
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-500 text-lg">Your wishlist is empty.</p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-4 bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600">Browse Products</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($wishlistItems as $item)
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            @if($item->product->image)
                                <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">No Image</div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold">{{ $item->product->name }}</h3>
                                <p class="text-lg font-bold text-indigo-600 mt-1">Tsh {{ number_format($item->product->currentPrice(), 0) }}</p>
                                <div class="mt-3 flex gap-2">
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded text-sm hover:bg-green-600">Add to Cart</button>
                                    </form>
                                    <form action="{{ route('wishlist.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 text-sm hover:underline">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
