<?php

namespace PicPay\Prokers\Factory;

use PicPay\Prokers\ConnectionManager;
use PicPay\Prokers\Config;
use PicPay\Prokers\Consumer;
use PicPay\Prokers\Contracts\ConsumerInterface;
use PicPay\Prokers\Contracts\ProducerInterface;
use PicPay\Prokers\Producer;
use Psr\Log\LoggerInterface;

class BrokerFactory
{
    private $connection;

    public function __construct(array $config)
    {
        $this->connection = new ConnectionManager(new Config($config));
    }

    public function setBroker(string $broker): void
    {
        $this->connection->setConnection($broker);
    }

    public function createProducer(): ProducerInterface
    {
        return new Producer($this->connection);
    }

    public function createConsumer(): ConsumerInterface
    {
        return new Consumer($this->connection);
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->connection->setLogger($logger);
        return $this;
    }
}
