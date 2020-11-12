<?php

namespace PicPay\Prokers;

use Interop\Queue\Message;
use PicPay\Prokers\Contracts\ConnectionManagerInterface;
use PicPay\Prokers\Contracts\ProducerInterface;

/**
 * Class Producer
 *
 * @package PicPay\Prokers
 */
final class Producer implements ProducerInterface
{

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var array
     */
    private $headers;

    /**
     * @var int
     */
    private $deliveryDelay;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var int
     */
    private $timeToLive;

    /**
     * @var ConnectionManagerInterface
     */
    private $connectionManager;

    /**
     * @var string
     */
    private $correlationId;

    /**
     * Producer constructor.
     *
     * @param ConnectionManagerInterface $connectionManager
     */
    public function __construct(ConnectionManagerInterface $connectionManager)
    {
        $this->connectionManager = $connectionManager;
        $this->defaultHeaders();
    }

    /**
     * @inheritDoc
     */
    public function connection(string $name): ProducerInterface
    {
        $this->connectionManager->setConnection($name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function produce(string $destination, array $payload = []): void
    {
        $context = $this->connectionManager->getContext($destination);

        $this->connectionManager
            ->getProducer($destination)
            ->send(
                $context->createTopic($destination),
                $this->createMessage($destination, $payload)
            );
    }

    /**
     * @inheritDoc
     */
    public function setProperties(array $properties): ProducerInterface
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHeaders(array $headers): ProducerInterface
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryDelay(int $deliveryDelay): ProducerInterface
    {
        $this->deliveryDelay = $deliveryDelay;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPriority(int $priority): ProducerInterface
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTimeToLive(int $timeToLive): ProducerInterface
    {
        $this->timeToLive = $timeToLive;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCorrelationId(string $correlationId): ProducerInterface
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    /**
     * Create message
     *
     * @param string $destination
     * @param array $payload
     * @return Message
     */
    private function createMessage(string $destination, array $payload)
    {
        $message = $this->connectionManager
            ->getContext($destination)
            ->createMessage(
                json_encode($payload, true),
                $this->properties,
                $this->headers
            );

        if (!empty($this->correlationId)) {
            $message->setCorrelationId($this->correlationId);
        }

        return $message;
    }

    private function defaultHeaders(): void
    {
        $this->headers['contentType'] = 'application/json';

        if ($appName = getenv('APP_NAME')) {
            $this->headers['applicationName'] = $appName;
        }
    }
}
