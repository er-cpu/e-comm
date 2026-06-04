<x-app-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Receipt — Order #{{ $order->id }}</h4>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 2H5v1h6z"/><path d="M3 5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 1h10a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1m1 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/></svg>
                    Print
                </button>
                <a href="{{ route('receipt.download', $order) }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
                    Download PDF
                </a>
            </div>
        </div>

        <div class="card shadow-sm" id="receipt">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">SmartTrade Africa Ltd</h3>
                        <p class="mb-0 text-muted small">Receipt</p>
                    </div>
                    <div class="text-end">
                        <p class="mb-0"><strong>Order #{{ $order->id }}</strong></p>
                        <p class="mb-0 text-muted small">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col">
                        <p class="mb-0 text-muted small fw-semibold">Bill To</p>
                        <p class="mb-0">{{ $order->user->first_name }} {{ $order->user->last_name }}</p>
                        <p class="mb-0 text-muted small">{{ $order->user->email }}</p>
                        <p class="mb-0 text-muted small">{{ $order->user->phone ?? 'No phone' }}</p>
                    </div>
                    <div class="col text-end">
                        <p class="mb-0 text-muted small fw-semibold">Payment</p>
                        <p class="mb-0 text-capitalize">{{ $order->payment?->gateway ?? 'N/A' }}</p>
                        <p class="mb-0 text-muted small">Ref: {{ $order->payment?->gateway_reference ?? 'N/A' }}</p>
                        <p class="mb-0 text-muted small">Status: <span class="text-success fw-semibold">Paid</span></p>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">Tsh {{ number_format($item->price, 0) }}</td>
                                <td class="text-end fw-semibold">Tsh {{ number_format($item->price * $item->quantity, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold fs-5">Tsh {{ number_format($order->total_price, 0) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="text-center text-muted small mt-4 pt-3 border-top">
                    <p class="mb-0">Thank you for your purchase!</p>
                    <p class="mb-0">SmartTrade Africa Ltd</p>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:underline">&larr; Back to Order</a>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            nav, .btn, .container > .d-flex, .container > a { display: none !important; }
            #receipt { border: none !important; box-shadow: none !important; }
            .card { border: none !important; }
        }
    </style>
    @endpush
</x-app-layout>
