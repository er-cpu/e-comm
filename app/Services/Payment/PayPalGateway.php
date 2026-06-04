<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;

class PayPalGateway implements PaymentGatewayInterface
{
    protected ?string $clientId = null;

    protected ?string $secret = null;

    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('payment.gateways.paypal.client_id');
        $this->secret = config('payment.gateways.paypal.secret');
        $this->baseUrl = config('payment.gateways.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->secret);
    }

    public function getName(): string
    {
        return 'paypal';
    }

    public function getDisplayName(): string
    {
        return 'PayPal';
    }

    protected function getAccessToken(): string
    {
        throw_unless($this->isConfigured(), new \RuntimeException('PayPal is not configured.'));

        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        $response->throw();

        return $response->json('access_token');
    }

    public function checkout(array $params): array
    {
        $items = [];
        $total = 0;

        foreach ($params['items'] as $item) {
            $unitAmount = number_format($item['price'], 2, '.', '');
            $items[] = [
                'name' => $item['name'],
                'unit_amount' => [
                    'currency_code' => strtoupper($params['currency'] ?? 'TZS'),
                    'value' => $unitAmount,
                ],
                'quantity' => $item['quantity'],
            ];
            $total += $item['price'] * $item['quantity'];
        }

        $accessToken = $this->getAccessToken();

        $orderPayload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'items' => $items,
                    'amount' => [
                        'currency_code' => strtoupper($params['currency'] ?? 'TZS'),
                        'value' => number_format($total, 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => strtoupper($params['currency'] ?? 'TZS'),
                                'value' => number_format($total, 2, '.', ''),
                            ],
                        ],
                    ],
                ],
            ],
            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                        'landing_page' => 'LOGIN',
                        'user_action' => 'PAY_NOW',
                        'return_url' => $params['success_url'],
                        'cancel_url' => $params['cancel_url'],
                    ],
                ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->withHeader('Content-Type', 'application/json')
            ->post("{$this->baseUrl}/v2/checkout/orders", $orderPayload);

        $response->throw();
        $data = $response->json();

        $approveLink = collect($data['links'] ?? [])
            ->firstWhere('rel', 'payer-action');

        return [
            'status' => 'redirect',
            'redirect_url' => $approveLink['href'] ?? $data['links'][0]['href'],
            'gateway_reference' => $data['id'],
            'gateway_data' => $data,
        ];
    }

    public function complete(array $data): array
    {
        $orderId = $data['token'] ?? $data['order_id'] ?? null;

        if (!$orderId) {
            return ['status' => 'failed', 'message' => 'Missing order ID.'];
        }

        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->withHeader('Content-Type', 'application/json')
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        if (!$response->successful()) {
            return [
                'status' => 'failed',
                'message' => 'PayPal capture failed.',
                'gateway_data' => $response->json(),
            ];
        }

        $result = $response->json();

        $status = match ($result['status']) {
            'COMPLETED' => 'completed',
            default => 'failed',
        };

        $capture = $result['purchase_units'][0]['payments']['captures'][0] ?? [];

        return [
            'status' => $status,
            'gateway_reference' => $orderId,
            'gateway_data' => $result,
            'amount' => $capture['amount']['value'] ?? 0,
            'currency' => $capture['amount']['currency_code'] ?? 'TZS',
        ];
    }

    public function verify(string $reference): array
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->get("{$this->baseUrl}/v2/checkout/orders/{$reference}");

        if (!$response->successful()) {
            return ['status' => 'failed', 'message' => 'Could not verify order.'];
        }

        $data = $response->json();

        $status = match ($data['status']) {
            'COMPLETED' => 'completed',
            'APPROVED' => 'pending',
            'SAVED' => 'pending',
            default => 'failed',
        };

        return [
            'status' => $status,
            'gateway_reference' => $reference,
            'gateway_data' => $data,
        ];
    }
}
