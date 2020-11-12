<?php

namespace PicPay\Prokers\Contracts;

use Enqueue\Consumption\ExtensionInterface;
use Interop\Queue\Processor;
use Psr\Log\LoggerInterface;

interface ConsumerInterface
{
    /**
     * Start consumer
     *
     * @param string $destination
     * @param Processor $processor
     * @throws \Exception
     */
    public function consume(string $destination, Processor $processor): void;

    /**
     * Set connection
     *
     * @param string|null $name
     * @return ConsumerInterface
     */
    public function setConnection(?string $name = null): ConsumerInterface;

    /**
     * Set Group.id
     *
     * @param string|null $groupId
     * @return ConsumerInterface
     */
    public function setGroupId(?string $groupId): ConsumerInterface;

    /**
     * Add extension to consumer, the extension must implement at last one of the interfaces bellow:
     * - Enqueue\Consumption\ExtensionInterface
     * - Enqueue\Consumption\StartExtensionInterface
     * - Enqueue\Consumption\PreSubscribeExtensionInterface
     * - Enqueue\Consumption\PreConsumeExtensionInterface
     * - Enqueue\Consumption\MessageReceivedExtensionInterface
     * - Enqueue\Consumption\PostMessageReceivedExtensionInterface
     * - Enqueue\Consumption\MessageResultExtensionInterface
     * - Enqueue\Consumption\ProcessorExceptionExtensionInterface
     * - Enqueue\Consumption\PostConsumeExtensionInterface
     * - Enqueue\Consumption\EndExtensionInterface
     * - Enqueue\Consumption\InitLoggerExtensionInterface
     *
     * @param $extension
     * @return ConsumerInterface
     */
    public function addExtension($extension): ConsumerInterface;

    /**
     * Set receive timeout
     *
     * @param int $receiveTimeoutMs
     * @return ConsumerInterface
     */
    public function setTimeout(int $receiveTimeoutMs): ConsumerInterface;

    /**
     * Set the amount of attempts to consumer process the message
     *
     * @param int $attempts
     * @param int $intervalMs time in milliseconds
     * @return ConsumerInterface
     */
    public function setAttempts(int $attempts = 1, int $intervalMs = 1000): ConsumerInterface;

    /**
     * Set the name of DLQ to send the message if any failure occur in consumer process
     *
     * @param string $target
     * @return ConsumerInterface
     */
    public function setDlq(string $target): ConsumerInterface;

    /**
     * @return ConnectionManagerInterface
     */
    public function getConnectionManager(): ConnectionManagerInterface;
}
