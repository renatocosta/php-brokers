<?php

namespace Tests\Unit;

use Enqueue\RdKafka\RdKafkaProducer;
use Enqueue\Redis\RedisProducer;
use Enqueue\Pheanstalk\PheanstalkProducer;
use Interop\Queue\Context;
use Interop\Queue\Producer;
use Mockery as m;
use PicPay\Prokers\ConnectionManager;
use PicPay\Prokers\Config;
use PicPay\Prokers\LogHandler;
use PicPay\Prokers\Exceptions\InvalidBrokerConfigurationException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ConnectionManagerTest extends TestCase
{
    /**
     * @var array
     */
    protected $configFile;

    /**
     * @var array
     */
    protected $multipleQueueConfig;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->configFile = require __DIR__ . '/../data/enqueue.php';
        $this->multipleQueueConfig = [
            'default' => 'rdkafka',
            'connections' => [
                'rdkakfa' => [
                    'commmon' => ['some' => 'configuration'],
                    'queues' => [
                        'phpunit-test' => [
                            'another' => 'configuration',
                        ],
                        'another-phpunit-test' => [
                            'another' => 'configuration',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function brokerProvider()
    {
        return [
            ['rdkafka', RdKafkaProducer::class],
            ['beanstalkd', PheanstalkProducer::class],
            ['redis', RedisProducer::class],
        ];
    }

    public function testGetDefaultConnectionShouldReturnValueFromConfig()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('getDefaultConnection')
            ->andReturn('some-connection');

        $connectionManager = new ConnectionManager($config);

        $this->assertEquals(
            'some-connection',
            $connectionManager->getDefaultConnection()
        );
    }

    public function testSetValidConnectionShouldUpdateAttribute()
    {
        $config = new Config($this->configFile);
        $connectionManager = new ConnectionManager($config);
        $connection = 'rdkafka';

        $connectionManager->setConnection($connection);

        $this->assertAttributeEquals(
            $connection,
            'connection',
            $connectionManager
        );
    }

    public function testSetInvalidConnectionShouldThrowAnException()
    {
        $config = new Config($this->configFile);
        $connectionManager = new ConnectionManager($config);
        $connection = 'invalid-connection';

        $this->expectException(InvalidBrokerConfigurationException::class);
        $this->expectExceptionMessage(
            'Broker connection [invalid-connection] not configured.'
        );

        $connectionManager->setConnection($connection);
    }

    /**
     * @dataProvider brokerProvider
     */
    public function testCreateProducer($broker, $instanceToCompare)
    {
        $config = new Config($this->configFile);
        $connectionManager = new ConnectionManager($config);
        $connectionManager->setConnection($broker);

        $producer = $connectionManager->getProducer();

        $this->assertInstanceOf($instanceToCompare, $producer);
    }

    /**
     * @dataProvider brokerProvider
     */
    public function testAssertContracts($broker)
    {
        $config = new Config($this->configFile);
        $connectionManager = new ConnectionManager($config);
        $connectionManager->setConnection($broker);

        $this->assertInstanceOf(
            Context::class,
            $connectionManager->getContext()
        );
        $this->assertInstanceOf(
            Producer::class,
            $connectionManager->getProducer()
        );
        $this->assertInstanceOf(
            LoggerInterface::class,
            $connectionManager->getLogger()
        );
    }

    public function testSetLoggerShouldCreateAnInstanceOfLogHandler()
    {
        $config = new Config($this->configFile);
        $connectionManager = new ConnectionManager($config);
        $logger = new NullLogger();

        $connectionManager->setLogger($logger);

        $this->assertInstanceOf(
            LogHandler::class,
            $connectionManager->getLogger()
        );
    }
}
