<?php

namespace PicPay\Prokers\Contracts;

use Interop\Queue\Exception;

interface ProducerInterface
{
    /**
     * Producer customization to use standard adopted
     *
     * @param string $destination
     * @param array $payload
     * @throws Exception
     * @throws \Exception
     */
    public function produce(string $destination, array $payload = []): void;

    /**
     * Set connection
     *
     * @param string $name
     * @return ProducerInterface
     */
    public function connection(string $name): ProducerInterface;

    /**
     * @param array $properties
     * @return ProducerInterface
     */
    public function setProperties(array $properties): ProducerInterface;

    /**
     * @param array $headers
     * @return ProducerInterface
     */
    public function setHeaders(array $headers): ProducerInterface;

    /**
     * @param int $deliveryDelay
     * @return ProducerInterface
     */
    public function setDeliveryDelay(int $deliveryDelay): ProducerInterface;

    /**
     * @param int $priority
     * @return ProducerInterface
     */
    public function setPriority(int $priority): ProducerInterface;

    /**
     * @param int $timeToLive
     * @return ProducerInterface
     */
    public function setTimeToLive(int $timeToLive): ProducerInterface;

    /**
     * Sets the correlation ID for the message.
     * A client can use the correlation header field to link one message with another.
     * A typical use is to link a response message with its request message.
     *
     * @param string $correlationId
     * @return ProducerInterface
     */
    public function setCorrelationId(string $correlationId): ProducerInterface;
}
