<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $product->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="md:flex">
                    <div class="md:w-1/2">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-96 object-cover">
                        @else
                            <div class="w-full h-96 bg-gray-200 flex items-center justify-center text-gray-400">No Image</div>
                        @endif
                    </div>
                    <div class="p-8 md:w-1/2">
                        <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
                        @if($product->category)
                            <p class="text-gray-500 mt-1">Category: {{ $product->category->name }}</p>
                        @endif
                        <p class="text-4xl font-bold text-indigo-600 mt-4">
                            Tsh {{ number_format($product->currentPrice(), 0) }}
                            @if($product->hasDiscount())
                                <span class="text-xl text-gray-400 line-through ml-2">Tsh {{ number_format($product->price, 0) }}</span>
                                <span class="text-base text-red-500 font-semibold ml-1">-{{ $product->discount_percent }}%</span>
                            @endif
                        </p>
                        <p class="mt-4 text-gray-700">{{ $product->description }}</p>
                        <p class="mt-4 {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $product->stock > 0 ? "In Stock: $product->stock available" : 'Out of Stock' }}
                        </p>

                        @auth
                            <form action="{{ route('cart.store') }}" method="POST" class="mt-6">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="flex gap-4 items-end">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="mt-1 rounded border-gray-300 w-20" {{ $product->stock < 1 ? 'disabled' : '' }}>
                                    </div>
                                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600" {{ $product->stock < 1 ? 'disabled' : '' }}>Add to Cart</button>
                                </div>
                            </form>

                            <form action="{{ route('wishlist.store') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="text-pink-500 hover:text-pink-600">Add to Wishlist</button>
                            </form>

                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', '+255 700 600 500') }}?text={{ urlencode('Hello, I would like to place an order for ' . $product->name) }}"
                               target="_blank"
                               class="btn btn-success mt-3 d-inline-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.324-.445-.33-.126-.004-.266-.004-.406-.004a.78.78 0 0 0-.562.27c-.197.232-.75.733-.75 1.788s.768 2.07.876 2.216c.109.146 1.514 2.313 3.67 3.243.512.222.912.354 1.223.452.513.16.98.137 1.35.083.41-.06 1.257-.514 1.434-1.01.177-.497.177-.92.124-1.01-.053-.09-.197-.148-.394-.247"/></svg>
                                Ask on WhatsApp
                            </a>
                        @else
                            <p class="mt-4 text-gray-500"><a href="{{ route('login') }}" class="text-indigo-600 underline">Login</a> to add to cart.</p>
                        @endauth

                        <div class="mt-6 flex items-center gap-3">
                            <span class="text-sm text-gray-500">Share:</span>
                            <a href="https://wa.me/?text={{ urlencode($product->name.' - Tsh '.number_format($product->currentPrice(), 0).' '.route('products.show', $product)) }}" target="_blank" class="text-green-500 hover:text-green-600" title="Share on WhatsApp">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.324-.445-.33-.126-.004-.266-.004-.406-.004a.78.78 0 0 0-.562.27c-.197.232-.75.733-.75 1.788s.768 2.07.876 2.216c.109.146 1.514 2.313 3.67 3.243.512.222.912.354 1.223.452.513.16.98.137 1.35.083.41-.06 1.257-.514 1.434-1.01.177-.497.177-.92.124-1.01-.053-.09-.197-.148-.394-.247"/></svg>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('products.show', $product)) }}" target="_blank" class="text-blue-600 hover:text-blue-700" title="Share on Facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/></svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($product->name.' - Tsh '.number_format($product->currentPrice(), 0)) }}&url={{ urlencode(route('products.show', $product)) }}" target="_blank" class="text-gray-700 hover:text-black" title="Share on Twitter">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold">Ratings & Reviews</h3>
                    @if($averageRating)
                        <div class="flex items-center gap-2">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="{{ $i <= round($averageRating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1">
                                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-gray-600 text-sm">({{ number_format($averageRating, 1) }} / 5)</span>
                        </div>
                    @endif
                </div>

                @auth
                    <form action="{{ route('products.rating.store', $product) }}" method="POST" class="mb-8 p-4 bg-gray-50 rounded">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Rating</label>
                            <div class="rating-stars d-flex gap-1 flex-row-reverse justify-content-end">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" class="d-none" {{ $userRating && $userRating->rating == $i ? 'checked' : '' }}>
                                    <label for="star{{ $i }}" class="cursor-pointer text-gray-300 hover:text-yellow-400" style="cursor: pointer;" onmouseover="this.style.color='#facc15'" onmouseout="this.style.color=this.previousElementSibling?.checked?'#facc15':'#d1d5db'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                            @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="review" class="block text-sm font-medium text-gray-700 mb-1">Review (optional)</label>
                            <textarea name="review" id="review" rows="3" class="w-full rounded border-gray-300 text-sm">{{ $userRating->review ?? '' }}</textarea>
                            @error('review') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded text-sm hover:bg-indigo-600">
                            {{ $userRating ? 'Update Rating' : 'Submit Rating' }}
                        </button>
                    </form>
                @endauth

                @forelse($product->ratings->sortByDesc('created_at') as $rating)
                    <div class="border-b border-gray-100 py-4 last:border-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-sm">{{ $rating->user->first_name }} {{ $rating->user->last_name }}</span>
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="{{ $i <= $rating->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1">
                                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-xs text-gray-400">{{ $rating->created_at->diffForHumans() }}</span>
                        </div>
                        @if($rating->review)
                            <p class="text-gray-700 text-sm">{{ $rating->review }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No ratings yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
