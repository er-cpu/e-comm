<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()?->isAdmin()) {
            abort(403);
        }

        $order->load('items.product', 'payment');

        return view('receipt.show', compact('order'));
    }

    public function download(Order $order)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()?->isAdmin()) {
            abort(403);
        }

        $order->load('items.product', 'payment');

        $pdf = Pdf::loadView('receipt.pdf', compact('order'));

        return $pdf->download("receipt-order-{$order->id}.pdf");
    }
}
