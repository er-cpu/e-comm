<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment #{{ $payment->id }}</h2>
            <a href="{{ route('admin.payments') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
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
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Payment Details</h4>
                            <table class="table table-sm">
                                <tr><td class="text-muted small pe-3">ID</td><td class="fw-semibold">#{{ $payment->id }}</td></tr>
                                <tr><td class="text-muted small pe-3">Amount</td><td class="fw-semibold">Tsh {{ number_format($payment->amount, 0) }}</td></tr>
                                <tr><td class="text-muted small pe-3">Currency</td><td>{{ strtoupper($payment->currency) }}</td></tr>
                                <tr><td class="text-muted small pe-3">Gateway</td><td>{{ $payment->gateway ?? '—' }}</td></tr>
                                <tr><td class="text-muted small pe-3">Gateway Ref</td><td class="text-xs">{{ $payment->gateway_reference ?? '—' }}</td></tr>
                                <tr><td class="text-muted small pe-3">Status</td>
                                    <td>
                                        <span class="px-2 py-1 rounded text-xs fw-semibold
                                            @if($payment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($payment->status === 'refunded') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr><td class="text-muted small pe-3">Date</td><td>{{ $payment->created_at->format('M d, Y H:i:s') }}</td></tr>
                            </table>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Customer</h4>
                            <table class="table table-sm">
                                <tr><td class="text-muted small pe-3">Name</td><td class="fw-semibold">{{ $payment->user?->first_name }} {{ $payment->user?->last_name }}</td></tr>
                                <tr><td class="text-muted small pe-3">Email</td><td>{{ $payment->user?->email ?? '—' }}</td></tr>
                                <tr><td class="text-muted small pe-3">Phone</td><td>{{ $payment->user?->phone ?? '—' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    @if($payment->order)
                        <hr class="my-4">
                        <h4 class="text-sm font-medium text-gray-500 mb-3">Order #{{ $payment->order->id }}</h4>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th class="small">Product</th><th class="small text-end">Qty</th><th class="small text-end">Price</th><th class="small text-end">Total</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->order->items as $item)
                                        <tr>
                                            <td class="small">{{ $item->product?->name ?? 'Deleted' }}</td>
                                            <td class="small text-end">{{ $item->quantity }}</td>
                                            <td class="small text-end">Tsh {{ number_format($item->price, 0) }}</td>
                                            <td class="small text-end fw-semibold">Tsh {{ number_format($item->price * $item->quantity, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr><td colspan="3" class="small text-end fw-bold">Total</td><td class="small text-end fw-bold">Tsh {{ number_format($payment->order->total_price, 0) }}</td></tr>
                                </tfoot>
                            </table>
                        </div>
                        <span class="px-2 py-1 rounded text-xs fw-semibold
                            @if($payment->order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->order->status === 'confirmed') bg-blue-100 text-blue-800
                            @elseif($payment->order->status === 'shipped') bg-purple-100 text-purple-800
                            @elseif($payment->order->status === 'completed') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($payment->order->status) }}
                        </span>
                    @endif

                    <div class="mt-4 d-flex gap-2">
                        @if($payment->status === 'pending')
                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm" onclick="return confirm('Verify this payment?')">Verify Payment</button>
                            </form>
                        @endif
                        @if(in_array($payment->status, ['completed', 'pending']) && (!$payment->order || $payment->order->status !== 'completed'))
                            <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Refund this payment?')">Refund Payment</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
