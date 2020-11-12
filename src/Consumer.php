<?php

namespace PicPay\Prokers;

use Enqueue\Consumption\ChainExtension;
use Enqueue\Consumption\Extension\SignalExtension;
use Enqueue\Consumption\QueueConsumer;
use Exception;
use Interop\Queue\Processor;
use InvalidArgumentException;
use PicPay\Prokers\Contracts\ConnectionManagerInterface;
use PicPay\Prokers\Contracts\ConsumerInterface;
use PicPay\Prokers\Exceptions\MessageSerializerException;
use PicPay\Prokers\Extensions\RetryAndDlqExtension;
use PicPay\Prokers\Extensions\ExtensionList;

/**
 * Class Consumer
 *
 * @package PicPay\Prokers
 */
final class Consumer implements ConsumerInterface
{
    /**
     * @var ConnectionManagerInterface
     */
    private $connectionManager;

    /**
     * @var \SplDoublyLinkedList
     */
    private $extensions;

    /**
     * @var int
     */
    private $receiveTimeoutMs = 20000;

    /**
     * @var int
     */
    private $attempts = 1;

    /**
     * @var int
     */
    private $retryIntervalMs = 1000;

    /**
     * @var string
     */
    private $dlq;

    /**
     * Consumer constructor.
     *
     * @param ConnectionManagerInterface $connectionManager
     */
    public function __construct(ConnectionManagerInterface $connectionManager)
    {
        $this->connectionManager = $connectionManager;
        $this->extensions = new ExtensionList();
    }

    /**
     * @inheritDoc
     */
    public function consume(string $destination, Processor $processor): void
    {
        $this->configureExtensions();
        $queueConsumer = new QueueConsumer(
            $this->connectionManager->getContext($destination),
            new ChainExtension($this->extensions->toArray()),
            [],
            $this->connectionManager->getLogger(),
            $this->receiveTimeoutMs
        );

        $queueConsumer->bind($destination, $processor);

        try {
            $queueConsumer->consume();
        } catch (InvalidArgumentException $e) {
            throw new MessageSerializerException($e->getMessage(), $e->getCode(), $e);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function setConnection(?string $name = null): ConsumerInterface
    {
        $this->connectionManager->setConnection($name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setGroupId(?string $groupId): ConsumerInterface
    {
        $this->connectionManager->setGroupId($groupId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExtension($extension): ConsumerInterface
    {
        $this->extensions->push($extension);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTimeout(int $receiveTimeoutMs): ConsumerInterface
    {
        $this->receiveTimeoutMs = $receiveTimeoutMs;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getConnectionManager(): ConnectionManagerInterface
    {
        return $this->connectionManager;
    }

    /**
     * @inheritDoc
     */
    public function setAttempts(int $attempts = 1, int $intervalMs = 1000): ConsumerInterface
    {
        $this->attempts = $attempts > 1 ? $attempts : 1;
        $this->retryIntervalMs = $intervalMs;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDlq(string $target): ConsumerInterface
    {
        $this->dlq = $target;
        return $this;
    }

    private function configureExtensions()
    {
        $this->extensions->unshift(new RetryAndDlqExtension(
            $this->attempts,
            $this->retryIntervalMs,
            $this->dlq
        ));
        $this->extensions->push(new SignalExtension());
    }
}
