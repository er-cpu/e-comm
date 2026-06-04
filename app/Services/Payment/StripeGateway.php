<?php

namespace App\Services\Payment;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function getName(): string
    {
        return 'stripe';
    }

    public function getDisplayName(): string
    {
        return 'Stripe (Credit Card)';
    }

    public function isConfigured(): bool
    {
        return !empty(config('services.stripe.secret'));
    }

    public function checkout(array $params): array
    {
        $currency = strtolower($params['currency'] ?? 'tzs');
        $lineItems = [];

        foreach ($params['items'] as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $this->toStripeAmount($item['price'], $currency),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $params['success_url'],
                'cancel_url' => $params['cancel_url'],
                'customer_email' => $params['customer_email'],
                'metadata' => [
                    'user_id' => $params['user_id'],
                ],
            ]);
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => $e->getMessage(),
                'gateway_data' => ['error' => $e->getMessage()],
            ];
        }

        return [
            'status' => 'redirect',
            'redirect_url' => $session->url,
            'gateway_reference' => $session->id,
            'gateway_data' => [
                'session_id' => $session->id,
                'payment_intent' => $session->payment_intent,
            ],
        ];
    }

    public function complete(array $data): array
    {
        $sessionId = $data['session_id'] ?? null;

        if (!$sessionId) {
            return ['status' => 'failed', 'message' => 'Missing session ID.'];
        }

        try {
            $session = Session::retrieve($sessionId);
        } catch (\Exception $e) {
            return ['status' => 'failed', 'message' => 'Could not verify payment session.'];
        }

        if ($session->payment_status !== 'paid') {
            return ['status' => 'failed', 'message' => 'Payment not completed.'];
        }

        $currency = $session->currency;

        return [
            'status' => 'completed',
            'gateway_reference' => $session->id,
            'gateway_data' => [
                'session_id' => $session->id,
                'payment_intent' => $session->payment_intent,
            ],
            'amount' => $this->fromStripeAmount($session->amount_total, $currency),
            'currency' => $currency,
        ];
    }

    public function verify(string $reference): array
    {
        try {
            $session = Session::retrieve($reference);
        } catch (\Exception $e) {
            return ['status' => 'failed', 'message' => 'Could not verify payment.'];
        }

        $currency = $session->currency;

        $status = match ($session->payment_status) {
            'paid' => 'completed',
            'unpaid' => 'pending',
            'no_payment_required' => 'completed',
            default => 'failed',
        };

        return [
            'status' => $status,
            'gateway_reference' => $session->id,
            'amount' => $this->fromStripeAmount($session->amount_total, $currency),
            'currency' => $currency,
        ];
    }

    protected function toStripeAmount(float $amount, string $currency): int
    {
        return $this->isZeroDecimal($currency) ? (int) $amount : (int) ($amount * 100);
    }

    protected function fromStripeAmount(int $amount, string $currency): float
    {
        return $this->isZeroDecimal($currency) ? (float) $amount : $amount / 100;
    }

    protected function isZeroDecimal(string $currency): bool
    {
        return in_array(strtoupper($currency), [
            'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW',
            'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF',
            'XOF', 'XPF', 'TZS',
        ]);
    }
}
