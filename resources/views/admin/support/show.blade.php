<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Support Ticket #{{ $message->id }}</h2>
            <a href="{{ route('admin.support.messages') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="fw-semibold">{{ $message->subject }}</h4>
                            <p class="text-muted small mb-0">
                                From <span class="fw-semibold">{{ $message->name }}</span>
                                &lt;{{ $message->email }}&gt;
                                @if($message->user)
                                    (User #{{ $message->user_id }})
                                @endif
                            </p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs fw-semibold
                            @if($message->status === 'open') bg-yellow-100 text-yellow-800
                            @elseif($message->status === 'replied') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($message->status) }}
                        </span>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h5 class="text-sm text-muted mb-2">Message</h5>
                        <div class="bg-gray-50 p-4 rounded text-sm">{{ nl2br(e($message->message)) }}</div>
                        <p class="text-xs text-muted mt-2">{{ $message->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    @if($message->admin_reply)
                        <div class="mb-4">
                            <h5 class="text-sm text-muted mb-2">Your Reply</h5>
                            <div class="bg-blue-50 p-4 rounded text-sm border-start border-4 border-primary">{{ nl2br(e($message->admin_reply)) }}</div>
                            <p class="text-xs text-muted mt-2">{{ $message->replied_at?->format('M d, Y H:i') }}</p>
                        </div>
                    @endif

                    @if($message->status !== 'closed')
                        <hr>
                        <h5 class="text-sm text-muted mb-3">Reply to {{ $message->name }}</h5>
                        <form method="POST" action="{{ route('admin.support.messages.reply', $message) }}">
                            @csrf
                            <div class="mb-3">
                                <textarea name="admin_reply" rows="5" class="form-control" placeholder="Type your reply..." required>{{ old('admin_reply') }}</textarea>
                                @error('admin_reply')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">Send Reply</button>
                                <form method="POST" action="{{ route('admin.support.messages.close', $message) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-dark btn-sm" onclick="return confirm('Close this ticket?')">Close Ticket</button>
                                </form>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-secondary py-2 small mb-0">This ticket is closed.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
