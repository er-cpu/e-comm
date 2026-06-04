<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;

class FlutterwaveGateway implements PaymentGatewayInterface
{
    protected ?string $secretKey = null;

    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('payment.gateways.flutterwave.secret_key');
        $this->baseUrl = 'https://api.flutterwave.com/v3';
    }

    public function isConfigured(): bool
    {
        return !empty($this->secretKey);
    }

    public function getName(): string
    {
        return 'flutterwave';
    }

    public function getDisplayName(): string
    {
        return 'Flutterwave';
    }

    public function checkout(array $params): array
    {
        $total = collect($params['items'])->sum(fn($i) => $i['price'] * $i['quantity']);

        $payload = [
            'tx_ref' => 'txn_' . uniqid(),
            'amount' => number_format($total, 2, '.', ''),
            'currency' => $params['currency'] ?? 'TZS',

            'currency' => $result['data']['currency'] ?? 'TZS',

            'currency' => $data['currency'] ?? 'TZS',
        ];
    }
}
