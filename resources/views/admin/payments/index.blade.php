<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment Management</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-bottom border-gray-200 bg-gray-50 d-flex align-items-center justify-content-between">
                    <div class="d-flex gap-2 align-items-center">
                        <span class="text-sm text-muted">All Payments</span>
                        <span class="badge bg-secondary">{{ $payments->total() }}</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-sm">ID</th>
                                <th class="px-4 py-3 text-sm">Customer</th>
                                <th class="px-4 py-3 text-sm">Order</th>
                                <th class="px-4 py-3 text-sm">Gateway</th>
                                <th class="px-4 py-3 text-sm">Amount</th>
                                <th class="px-4 py-3 text-sm">Status</th>
                                <th class="px-4 py-3 text-sm">Date</th>
                                <th class="px-4 py-3 text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm">#{{ $payment->id }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $payment->user?->first_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $payment->order_id ? '#' . $payment->order_id : '—' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $payment->gateway ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm">Tsh {{ number_format($payment->amount, 0) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs fw-semibold
                                            @if($payment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($payment->status === 'refunded') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                            @if($payment->order && $payment->order->status === 'completed')
                                                <span class="text-xs text-muted align-self-center">—</span>
                                            @else
                                                @if($payment->status === 'pending')
                                                    <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-outline-success" onclick="return confirm('Verify this payment?')">Verify</button>
                                                    </form>
                                                @endif
                                                @if(in_array($payment->status, ['completed', 'pending']))
                                                    <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Refund this payment?')">Refund</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-muted small">No payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
