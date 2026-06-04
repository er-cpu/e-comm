<?php

namespace App\Services\Payment;

use InvalidArgumentException;

class PaymentService
{
    protected array $gateways = [];

    protected ?string $defaultGateway = null;

    public function __construct()
    {
        $this->defaultGateway = config('payment.default', 'mock');
    }

    public function gateway(?string $name = null): PaymentGatewayInterface
    {
        $name = $name ?: $this->defaultGateway;

        if (isset($this->gateways[$name])) {
            return $this->gateways[$name];
        }

        $gateway = $this->resolve($name);
        $this->gateways[$name] = $gateway;

        return $gateway;
    }

    protected function resolve(string $name): PaymentGatewayInterface
    {
        return match ($name) {
            'stripe' => new StripeGateway(),
            'paypal' => new PayPalGateway(),
            'flutterwave' => new FlutterwaveGateway(),
            'mock' => new MockGateway(),
            default => throw new InvalidArgumentException("Payment gateway [{$name}] is not supported."),
        };
    }

    public function gateways(): array
    {
        $available = array_keys(config('payment.gateways', []));
        $result = [];

        foreach ($available as $name) {
            $enabled = config("payment.gateways.{$name}.enabled", false);
            if (!$enabled) {
                continue;
            }

            try {
                $gateway = $this->gateway($name);

                if (method_exists($gateway, 'isConfigured') && !$gateway->isConfigured()) {
                    continue;
                }

                $result[$name] = $gateway;
            } catch (InvalidArgumentException | \RuntimeException) {
                //
            }
        }

        return $result;
    }

    public function getDefaultGateway(): string
    {
        return $this->defaultGateway;
    }
}
