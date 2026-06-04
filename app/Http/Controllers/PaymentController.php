<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Notifications\OrderConfirmed;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function checkout()
    {
        $gateway = $this->paymentService->gateway();
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->withErrors(['stock' => "Insufficient stock for {$item->product->name}."]);
            }
        }

        $rate = (float) config('payment.tzs_to_usd_rate', 2400);

        $items = $cartItems->map(fn($i) => [
            'name' => $i->product->name,
            'price' => $i->product->currentPrice(),
            'quantity' => $i->quantity,
        ])->toArray();

        $totalTzs = collect($items)->sum(fn($i) => $i['price'] * $i['quantity']);

        $itemsUsd = array_map(fn($i) => [
            'name' => $i['name'],
            'price' => round($i['price'] / $rate, 2),
            'quantity' => $i['quantity'],
        ], $items);

        $totalUsd = collect($itemsUsd)->sum(fn($i) => $i['price'] * $i['quantity']);

        $result = $gateway->checkout([
            'items' => $itemsUsd,
            'total' => $totalUsd,
            'currency' => 'USD',
            'customer_email' => Auth::user()->email,
            'customer_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'user_id' => (string) Auth::id(),
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
        ]);

        if ($result['status'] === 'failed') {
            return back()->with('error', $result['message'] ?? 'Payment initiation failed.');
        }

        Payment::create([
            'user_id' => Auth::id(),
            'gateway' => $gateway->getName(),
            'gateway_reference' => $result['gateway_reference'],
            'gateway_data' => $result['gateway_data'] ?? null,
            'amount' => $totalTzs,
            'currency' => 'TZS',
            'status' => 'pending',
        ]);

        if ($result['status'] === 'redirect' && isset($result['redirect_url'])) {
            return redirect($result['redirect_url']);
        }

        return back()->with('error', 'Unexpected payment response.');
    }

    public function success(Request $request)
    {
        $gateway = $this->paymentService->gateway();
        $result = $gateway->complete($request->all());

        if ($result['status'] !== 'completed') {
            return redirect()->route('cart.index')
                ->with('error', $result['message'] ?? 'Payment was not completed.');
        }

        $payment = Payment::where('gateway', $gateway->getName())
            ->where('gateway_reference', $result['gateway_reference'])
            ->first();

        if (!$payment) {
            return redirect()->route('cart.index')->with('error', 'Payment record not found.');
        }

        $cartItems = Cart::with('product')->where('user_id', $payment->user_id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('orders.index')->with('info', 'Order already processed.');
        }

        DB::beginTransaction();

        try {
            $total = $cartItems->sum(fn($i) => $i->product->currentPrice() * $i->quantity);

            $order = Order::create([
                'user_id' => $payment->user_id,
                'total_price' => $total,
                'status' => 'confirmed',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->currentPrice(),
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            Cart::where('user_id', $payment->user_id)->delete();

            $payment->update([
                'order_id' => $order->id,
                'status' => 'completed',
                'gateway_data' => array_merge(
                    $payment->gateway_data ?? [],
                    $result['gateway_data'] ?? []
                ),
            ]);

            $payment->user->notify(new OrderConfirmed($order));

            ActivityLog::create([
                'user_id' => $payment->user_id,
                'action' => 'order_placed',
                'description' => "Order #{$order->id} placed — Tsh " . number_format($order->total_price, 0),
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Payment successful!');
        } catch (\Exception $e) {
            DB::rollBack();

            $payment->update([
                'status' => 'failed',
                'gateway_data' => array_merge(
                    $payment->gateway_data ?? [],
                    ['error' => $e->getMessage()]
                ),
            ]);

            return redirect()->route('cart.index')
                ->with('error', 'Order creation failed. Please contact support.');
        }
    }

    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Payment was cancelled.');
    }
}
