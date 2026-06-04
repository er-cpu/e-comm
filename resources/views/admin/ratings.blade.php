<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Ratings</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-4 py-3">User</th>
                            <th class="text-left px-4 py-3">Product</th>
                            <th class="text-left px-4 py-3">Rating</th>
                            <th class="text-left px-4 py-3">Review</th>
                            <th class="text-left px-4 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ratings as $rating)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $rating->user->first_name }} {{ $rating->user->last_name }}<br><span class="text-xs text-gray-400">{{ $rating->user->email }}</span></td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('products.show', $rating->product) }}" class="text-indigo-600 hover:underline">{{ $rating->product->name }}</a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="{{ $i <= $rating->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1">
                                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 max-w-xs">{{ $rating->review ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $rating->created_at->format('M j, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-8 text-gray-500">No ratings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $ratings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
