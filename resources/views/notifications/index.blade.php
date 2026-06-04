<x-app-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Notifications</h4>
            @if($notifications->whereNull('read_at')->count())
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm">Mark all as read</button>
                </form>
            @endif
        </div>

        @forelse($notifications as $notification)
            @php $data = $notification->data; @endphp
            <div class="card mb-2 shadow-sm {{ $notification->read_at ? 'border-light' : 'border-primary border-opacity-50 bg-light' }}">
                <div class="card-body d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-3">
                        @unless($notification->read_at)
                            <span class="badge bg-primary rounded-pill" style="width:8px;height:8px;padding:0;">&nbsp;</span>
                        @else
                            <span style="width:8px;"></span>
                        @endunless
                        <div>
                            <p class="mb-0 {{ $notification->read_at ? '' : 'fw-semibold' }}">
                                {{ $data['message'] ?? 'Notification' }}
                            </p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if(isset($data['url']))
                            <a href="{{ $data['url'] }}" class="btn btn-sm btn-outline-primary">View</a>
                        @endif
                        @unless($notification->read_at)
                            <form action="{{ route('notifications.read', $notification) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-secondary">Mark read</button>
                            </form>
                        @endunless
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <p class="fs-5">No notifications yet.</p>
            </div>
        @endforelse

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
