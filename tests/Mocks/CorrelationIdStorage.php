<?php

namespace Tests\Mocks;

use PicPay\Prokers\Contracts\CorrelationIdStorageInterface;

class CorrelationIdStorage implements CorrelationIdStorageInterface
{
    private $correlationId;

    public function saveCorrelationId(?string $id): void
    {
        $this->correlationId = $id;
    }

    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }
}
