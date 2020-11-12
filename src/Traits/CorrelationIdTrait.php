<?php

namespace PicPay\Prokers\Traits;

trait CorrelationIdTrait
{
    private $correlationIdKey = 'correlationId';

    public function setCorrelationId(string $correlationId = null): void
    {
        $this->setHeader($this->correlationIdKey, (string) $correlationId);
    }

    public function getCorrelationId(): ?string
    {
        return $this->getHeader($this->correlationIdKey);
    }
}
