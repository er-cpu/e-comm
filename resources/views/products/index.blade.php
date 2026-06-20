<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
                @auth
                    <div class="flex items-center gap-2 mt-1 text-xs">
                        <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded">Fingerprint Verified Login</span>
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded">Secure Payment via Stripe</span>
                    </div>
                @endauth
            </div>
            <form method="GET" class="flex flex-wrap gap-2 w-full sm:w-auto">
                <select name="category" class="rounded border-gray-300 text-sm text-gray-900 bg-white flex-1 min-w-[10rem] sm:flex-none" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}" class="rounded border-gray-300 text-sm text-gray-900 placeholder-gray-700 bg-white flex-1 min-w-[11rem]">
                <button type="submit" class="bg-indigo-500 text-white px-3 py-2 rounded text-sm">Search</button>
                @if(request()->filled('category') || request()->filled('search'))
                    <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-3 py-2 rounded text-sm text-decoration-none">Clear</a>
                @endif
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="product-grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6 fade-in">
                @forelse($products as $product)
                    <div class="bg-white rounded-lg shadow overflow-hidden hover-lift" style="transition-delay: {{ $loop->index * 0.03 }}s;">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="product-card-image w-full h-32 sm:h-48 object-cover">
                        @else
                            <div class="product-card-image w-full h-32 sm:h-48 bg-gray-200 flex items-center justify-center text-gray-400 text-xs sm:text-base">No Image</div>
                        @endif
                        <div class="p-3 sm:p-4">
                            <h3 class="font-semibold text-sm sm:text-lg leading-snug">{{ $product->name }}</h3>
                            @if($product->category)
                                <p class="text-xs sm:text-sm text-gray-500">{{ $product->category->name }}</p>
                            @endif
                            @php $avg = $product->ratings->avg('rating'); @endphp
                            @if($avg)
                                <div class="flex items-center gap-1 mt-1 text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16" fill="{{ $i <= round($avg) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1">
                                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                        </svg>
                                    @endfor
                                    <span class="text-xs text-gray-500">({{ number_format($avg, 1) }})</span>
                                </div>
                            @endif
                            <p class="hidden sm:block text-gray-600 text-sm mt-1">{{ Str::limit($product->description, 80) }}</p>
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mt-2 sm:mt-3 gap-1">
                                <div>
                                    <span class="text-sm sm:text-xl font-bold text-indigo-600">Tsh {{ number_format($product->currentPrice(), 0) }}</span>
                                    @if($product->hasDiscount())
                                        <span class="text-xs sm:text-sm text-gray-400 line-through ml-1">Tsh {{ number_format($product->price, 0) }}</span>
                                        <span class="text-xs text-red-500 font-semibold ml-1">-{{ $product->discount_percent }}%</span>
                                    @endif
                                </div>
                                <span class="text-xs sm:text-sm {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->stock > 0 ? "In Stock ($product->stock)" : 'Out of Stock' }}
                                </span>
                            </div>
                            <div class="mt-2 sm:mt-3 flex flex-col sm:flex-row gap-1.5 sm:gap-2">
                                <a href="{{ route('products.show', $product) }}" class="flex-1 text-center bg-indigo-500 text-white px-2 sm:px-3 py-1.5 sm:py-2 rounded text-xs sm:text-sm hover:bg-indigo-600">View</a>
                                @auth
                                    <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-full bg-green-500 text-white px-2 sm:px-3 py-1.5 sm:py-2 rounded text-xs sm:text-sm hover:bg-green-600" {{ $product->stock < 1 ? 'disabled' : '' }}>Add</button>
                                    </form>
                                    <form action="{{ route('wishlist.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit" class="w-full bg-pink-500 text-white px-2 sm:px-3 py-1.5 sm:py-2 rounded text-xs sm:text-sm hover:bg-pink-600">Wish</button>
                                    </form>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">No products found.</div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
