<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Activity History — All Users</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Action</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Description</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($activities as $log)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ $log->user?->name ?? 'Deleted User' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        @switch($log->action)
                                            @case('login') bg-blue-100 text-blue-800 @break
                                            @case('registered') bg-green-100 text-green-800 @break
                                            @case('order_placed') bg-purple-100 text-purple-800 @break
                                            @case('profile_updated') bg-yellow-100 text-yellow-800 @break
                                            @case('password_changed') bg-red-100 text-red-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $log->description }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No activity recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
