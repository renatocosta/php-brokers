<?php

namespace PicPay\Prokers\Contracts;

use Interop\Queue\Context;
use Interop\Queue\Producer;
use Psr\Log\LoggerInterface;

/**
 * Interface ConnectionInterface
 * @package PicPay\Prokers
 */
interface ConnectionManagerInterface
{
    /**
     * Initialize connection by name
     *
     * @param string $name
     */
    public function setConnection(?string $name): ConnectionManagerInterface;

    /**
     * Set logger
     *
     * @param LoggerInterface|null $logger
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger = null);

    /**
     * Get logger
     *
     * @return LoggerInterface
     */
    public function getLogger(): LogHandlerInterface;

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection();

    /**
     * Get connection context.
     *
     * @return Context
     */
    public function getContext(): Context;

    /**
     * Get producer
     *
     * @return Producer
     */
    public function getProducer(): Producer;

    /**
     * Set group.id
     */
    public function setGroupId(?string $groupId): ConnectionManagerInterface;

    /**
     * Get group.id
     */
    public function getGroupId(): ?string;
}
