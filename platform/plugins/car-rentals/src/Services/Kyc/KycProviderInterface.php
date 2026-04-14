<?php

namespace Botble\CarRentals\Services\Kyc;

interface KycProviderInterface
{
    public function verify(array $payload): array;
}
