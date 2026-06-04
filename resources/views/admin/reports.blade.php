<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Analytics & Reports</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Revenue & Orders Overview --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-xs">Total Revenue</p>
                    <p class="text-lg font-bold">Tsh {{ number_format($totalRevenue, 0) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-xs">Total Orders</p>
                    <p class="text-lg font-bold">{{ $totalOrders }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-xs">Confirmed</p>
                    <p class="text-lg font-bold text-blue-600">{{ $confirmedOrders }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-xs">Shipped</p>
                    <p class="text-lg font-bold text-purple-600">{{ $shippedOrders }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-gray-500 text-xs">Completed</p>
                    <p class="text-lg font-bold text-green-600">{{ $completedOrders }}</p>
                </div>
            </div>

            {{-- Order Status Distribution --}}
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Order Status Distribution</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <canvas id="orderStatusChart" height="140"></canvas>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="table-responsive w-100">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr><th class="small">Status</th><th class="small text-end">Orders</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td class="small"><span class="badge bg-yellow-100 text-yellow-800">Pending</span></td><td class="small text-end">{{ $totalOrders - $confirmedOrders - $shippedOrders - $completedOrders - $refundedOrders }}</td></tr>
                                    <tr><td class="small"><span class="badge bg-blue-100 text-blue-800">Confirmed</span></td><td class="small text-end">{{ $confirmedOrders }}</td></tr>
                                    <tr><td class="small"><span class="badge bg-purple-100 text-purple-800">Shipped</span></td><td class="small text-end">{{ $shippedOrders }}</td></tr>
                                    <tr><td class="small"><span class="badge bg-green-100 text-green-800">Completed</span></td><td class="small text-end">{{ $completedOrders }}</td></tr>
                                    <tr><td class="small"><span class="badge bg-red-100 text-red-800">Refunded</span></td><td class="small text-end">{{ $refundedOrders }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Monthly Revenue Histogram --}}
                <div class="bg-white rounded-lg shadow p-6" id="revenue">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Monthly Revenue (Last 6 Months)</h3>
                    @if($monthlyRevenue->isEmpty())
                        <p class="text-muted small text-center py-4">No data yet.</p>
                    @else
                        <canvas id="revenueChart" height="160"></canvas>
                        <div class="table-responsive mt-4">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr><th class="small">Month</th><th class="small text-end">Revenue (Tsh)</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyRevenue as $mr)
                                        <tr>
                                            <td class="small">{{ $mr->month }}</td>
                                            <td class="small text-end fw-semibold">{{ number_format($mr->total, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Best Selling Products --}}
                <div class="bg-white rounded-lg shadow p-6" id="best-sellers">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Best Selling Products</h3>
                    @if($bestSellers->isEmpty())
                        <p class="text-muted small text-center py-4">No sales data yet.</p>
                    @else
                        <canvas id="bestSellersChart" height="160"></canvas>
                        <div class="table-responsive mt-4">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr><th class="small">Product</th><th class="small text-end">Sold</th><th class="small text-end">Revenue</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($bestSellers as $item)
                                        <tr>
                                            <td class="small">{{ $item->product?->name ?? 'Deleted' }}</td>
                                            <td class="small text-end fw-semibold">{{ $item->total_qty }}</td>
                                            <td class="small text-end">Tsh {{ number_format($item->total_revenue, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8" id="customers">
                {{-- Customer Activity --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Customers by Orders</h3>
                    @if($customerActivity->isEmpty())
                        <p class="text-muted small text-center py-4">No customer activity yet.</p>
                    @else
                        <canvas id="customerChart" height="160"></canvas>
                        <div class="table-responsive mt-4">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr><th class="small">Customer</th><th class="small text-end">Orders</th><th class="small text-end">Total Spent</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($customerActivity as $user)
                                        <tr>
                                            <td class="small">{{ $user->first_name }} {{ $user->last_name }}</td>
                                            <td class="small text-end fw-semibold">{{ $user->orders_count }}</td>
                                            <td class="small text-end">Tsh {{ number_format($user->total_spent ?? 0, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Recent Activity Log</h3>
                    @if($recentActivity->isEmpty())
                        <p class="text-muted small text-center py-4">No activity yet.</p>
                    @else
                        <div class="list-group list-group-flush" style="max-height:300px;overflow-y:auto;">
                            @foreach($recentActivity as $log)
                                <div class="list-group-item px-0 py-2 border-0 border-bottom border-gray-100">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="fw-semibold small">{{ $log->user?->first_name ?? 'System' }}</span>
                                            <span class="text-muted small">— {{ $log->action }}</span>
                                        </div>
                                        <small class="text-muted flex-shrink-0 ms-2">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($log->description)
                                        <p class="text-xs text-muted mb-0 mt-1">{{ $log->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.payments') }}" class="bg-white rounded-lg shadow p-6 text-center hover:bg-gray-50 hover-lift">
                    <p class="text-lg font-semibold text-indigo-600">Payment Reports</p>
                </a>
                <a href="{{ route('admin.products') }}" class="bg-white rounded-lg shadow p-6 text-center hover:bg-gray-50 hover-lift">
                    <p class="text-lg font-semibold text-indigo-600">Product Analytics</p>
                </a>
                <a href="{{ route('admin.users') }}" class="bg-white rounded-lg shadow p-6 text-center hover:bg-gray-50 hover-lift">
                    <p class="text-lg font-semibold text-indigo-600">Customer List</p>
                </a>
                <a href="{{ route('admin.orders') }}" class="bg-white rounded-lg shadow p-6 text-center hover:bg-gray-50 hover-lift">
                    <p class="text-lg font-semibold text-indigo-600">Order Reports</p>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const baseColor = 'rgba(99, 102, 241, 0.7)';
        const borderColor = 'rgba(99, 102, 241, 1)';

        // ── Order Status Doughnut ──
        const statusCanvas = document.getElementById('orderStatusChart');
        if (statusCanvas) {
            new Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Confirmed', 'Shipped', 'Completed', 'Refunded'],
                    datasets: [{
                        data: [
                            {{ $totalOrders - $confirmedOrders - $shippedOrders - $completedOrders - $refundedOrders }},
                            {{ $confirmedOrders }},
                            {{ $shippedOrders }},
                            {{ $completedOrders }},
                            {{ $refundedOrders }}
                        ],
                        backgroundColor: [
                            'rgba(234, 179, 8, 0.7)',
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(168, 85, 247, 0.7)',
                            'rgba(34, 197, 94, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                        ],
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } }
                    }
                }
            });
        }

        // ── Monthly Revenue Bar Chart ──
        const revenueCanvas = document.getElementById('revenueChart');
        if (revenueCanvas) {
            new Chart(revenueCanvas, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($monthlyRevenue as $mr)
                            '{{ $mr->month }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Revenue (Tsh)',
                        data: [
                            @foreach($monthlyRevenue as $mr)
                                {{ $mr->total }},
                            @endforeach
                        ],
                        backgroundColor: baseColor,
                        borderColor: borderColor,
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (val) {
                                    return 'Tsh ' + val.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // ── Best Sellers Bar Chart ──
        const bestCanvas = document.getElementById('bestSellersChart');
        if (bestCanvas) {
            const labels = [
                @foreach($bestSellers as $item)
                    '{{ $item->product?->name ? \Illuminate\Support\Str::limit($item->product->name, 20) : 'Deleted' }}',
                @endforeach
            ];
            const quantities = [
                @foreach($bestSellers as $item)
                    {{ $item->total_qty }},
                @endforeach
            ];
            new Chart(bestCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Units Sold',
                        data: quantities,
                        backgroundColor: 'rgba(168, 85, 247, 0.7)',
                        borderColor: 'rgba(168, 85, 247, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        // ── Customer Activity Bar Chart ──
        const custCanvas = document.getElementById('customerChart');
        if (custCanvas) {
            const labels = [
                @foreach($customerActivity as $user)
                    '{{ $user->first_name }}',
                @endforeach
            ];
            const orders = [
                @foreach($customerActivity as $user)
                    {{ $user->orders_count }},
                @endforeach
            ];
            new Chart(custCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Orders',
                        data: orders,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }
    });
    </script>
</x-app-layout>
