<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Customer Support</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-bottom border-gray-200 bg-gray-50 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex gap-3 align-items-center">
                        <span class="text-sm text-muted">All Messages</span>
                        <span class="badge bg-secondary">{{ $messages->total() }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-yellow-100 text-yellow-800 px-2 py-1">Open</span>
                        <span class="badge bg-blue-100 text-blue-800 px-2 py-1">Replied</span>
                        <span class="badge bg-gray-100 text-gray-800 px-2 py-1">Closed</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-sm">ID</th>
                                <th class="px-4 py-3 text-sm">From</th>
                                <th class="px-4 py-3 text-sm">Subject</th>
                                <th class="px-4 py-3 text-sm">Type</th>
                                <th class="px-4 py-3 text-sm">Status</th>
                                <th class="px-4 py-3 text-sm">Date</th>
                                <th class="px-4 py-3 text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $msg)
                                <tr class="@if($msg->status === 'open') table-warning @endif">
                                    <td class="px-4 py-3 text-sm">#{{ $msg->id }}</td>
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold small">{{ $msg->name }}</div>
                                        <div class="text-xs text-muted">{{ $msg->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $msg->subject }}</td>
                                    <td class="px-4 py-3 text-sm">{{ ucfirst($msg->type) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs fw-semibold
                                            @if($msg->status === 'open') bg-yellow-100 text-yellow-800
                                            @elseif($msg->status === 'replied') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($msg->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $msg->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.support.messages.show', $msg) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                            @if($msg->status !== 'closed')
                                                <form method="POST" action="{{ route('admin.support.messages.close', $msg) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-dark" onclick="return confirm('Close this ticket?')">Close</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-muted small">No support messages yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
