<?php

namespace PicPay\Prokers\Contracts;

interface CorrelationIdStorageInterface
{
    /**
     * @param string|null $id
     */
    public function saveCorrelationId(?string $id): void;

    /**
     * @return string|null
     */
    public function getCorrelationId(): ?string;
}