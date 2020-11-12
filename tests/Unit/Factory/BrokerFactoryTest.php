<?php

namespace Tests\Unit\Factory;

use PHPUnit\Framework\TestCase;
use PicPay\Prokers\Contracts\ConsumerInterface;
use PicPay\Prokers\Contracts\ProducerInterface;
use PicPay\Prokers\Factory\BrokerFactory;

class BrokerFactoryTest extends TestCase
{
    private $config;

    protected function setUp()
    {
        parent::setUp();
        $this->config = require __DIR__ . '/../../data/enqueue.php';
    }

    public function brokerProvider()
    {
        return [
            ['rdkafka'],
            ['beanstalkd'],
            ['redis'],
        ];
    }

    /**
     * @dataProvider brokerProvider
     */
    public function testShouldCreateProducerUsingGlobalConfig(string $broker)
    {
        $factory = new BrokerFactory($this->config);
        $factory->setBroker($broker);

        $this->assertInstanceOf(
            ProducerInterface::class,
            $factory->createProducer(),
            sprintf('Failed to assert producer instance')
        );
    }

    /**
     * @dataProvider brokerProvider
     */
    public function testShouldCreateConsumerUsingGlobalConfig(string $broker)
    {
        $factory = new BrokerFactory($this->config);
        $factory->setBroker($broker);

        $this->assertInstanceOf(
            ConsumerInterface::class,
            $factory->createConsumer(),
            sprintf('Failed to assert consumer instance')
        );
    }
}
