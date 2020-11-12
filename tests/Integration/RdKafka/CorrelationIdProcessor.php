<?php

namespace Tests\Integration\RdKafka;

use Enqueue\Consumption\Result;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;

class CorrelationIdProcessor implements Processor
{
    private $correlationId;

    public function __construct($correlationId)
    {
        $this->correlationId = $correlationId;
    }

    /**
     * @inheritDoc
     */
    public function process(Message $message, Context $context)
    {
        $expected = $this->correlationId;
        $actual = $message->getCorrelationId();

        if ($expected !== $actual) {
            throw new \InvalidArgumentException(sprintf(
                'The given correlation id [%s] is different from correlation id in message payload [%s]',
                $expected,
                $actual
            ));
        }

        return Result::ACK;
    }
}
