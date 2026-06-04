<?php

namespace App\Services\Payment;

use Illuminate\Support\Collection;

interface PaymentGatewayInterface
{
    public function getName(): string;

    public function getDisplayName(): string;

    public function checkout(array $params): array;

    public function complete(array $data): array;

    public function verify(string $reference): array;
}
