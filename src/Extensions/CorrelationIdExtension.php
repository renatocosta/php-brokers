<?php

namespace PicPay\Prokers\Extensions;

use Enqueue\Consumption\Context\MessageReceived;
use Enqueue\Consumption\MessageReceivedExtensionInterface;
use PicPay\Prokers\Contracts\CorrelationIdStorageInterface;

class CorrelationIdExtension implements MessageReceivedExtensionInterface
{
    /**
     * @var CorrelationIdStorageInterface
     */
    private $storage;

    public function __construct(CorrelationIdStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function onMessageReceived(MessageReceived $context): void
    {
        $this->storage->saveCorrelationId($context->getMessage()->getCorrelationId());
    }
}