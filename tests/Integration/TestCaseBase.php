<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PicPay\Prokers\Consumer;
use PicPay\Prokers\Factory\BrokerFactory;
use PicPay\Prokers\Producer;

class TestCaseBase extends TestCase
{
    protected const TOPIC_NAME = "test";

    /**
     * @var string
     */
    protected $broker;

    /**
     * @var Producer
     */
    protected $producer;

    /**
     * @var Consumer
     */
    protected $consumer;

    protected function setUp(): void
    {
        parent::setUp();
        $config = require __DIR__ . '/../data/enqueue.php';
        $factory = new BrokerFactory($config);
        $factory->setBroker($this->broker);
        $this->producer = $factory->createProducer();
        $this->consumer = $factory->createConsumer();
    }
}
