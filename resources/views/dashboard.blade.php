<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Welcome, {{ Auth::user()->first_name }}!</h3>
                    <p class="mb-6">Start exploring our products and enjoy shopping.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('products.index') }}" class="bg-indigo-50 p-6 rounded-lg text-center hover:bg-indigo-100 transition">
                            <p class="text-2xl font-bold text-indigo-600">Shop</p>
                            <p class="mt-2 font-medium">Browse Products</p>
                        </a>
                        <a href="{{ route('cart.index') }}" class="bg-green-50 p-6 rounded-lg text-center hover:bg-green-100 transition">
                            <p class="text-2xl font-bold text-green-600">Cart</p>
                            <p class="mt-2 font-medium">View Cart</p>
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="bg-pink-50 p-6 rounded-lg text-center hover:bg-pink-100 transition">
                            <p class="text-2xl font-bold text-pink-600">Heart</p>
                            <p class="mt-2 font-medium">Wishlist</p>
                        </a>
                        <a href="{{ route('orders.index') }}" class="bg-blue-50 p-6 rounded-lg text-center hover:bg-blue-100 transition">
                            <p class="text-2xl font-bold text-blue-600">Box</p>
                            <p class="mt-2 font-medium">My Orders</p>
                        </a>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <div class="mt-8 p-4 bg-yellow-50 rounded-lg">
                            <p class="font-semibold text-yellow-800">Admin Access</p>
                            <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:underline">Go to Admin Dashboard -&gt;</a>
                        </div>
                    @endif
                </div>
            </div>

            @php $testimonials = \App\Models\ProductRating::with(['user', 'product'])->where('rating', '>=', 4)->latest()->take(6)->get(); @endphp
            @if($testimonials->isNotEmpty())
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="text-yellow-400"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>
                        What Our Customers Say
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($testimonials as $rating)
                            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-400">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-bold text-indigo-600">
                                        {{ substr($rating->user->first_name, 0, 1) }}{{ substr($rating->user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">{{ $rating->user->first_name }} {{ $rating->user->last_name }}</p>
                                        <div class="flex text-yellow-400" style="font-size: 10px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16" fill="{{ $i <= $rating->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1">
                                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                @if($rating->review)
                                    <p class="text-sm text-gray-600 italic">"{{ $rating->review }}"</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-2">on <a href="{{ route('products.show', $rating->product) }}" class="text-indigo-600 hover:underline">{{ $rating->product->name }}</a></p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
