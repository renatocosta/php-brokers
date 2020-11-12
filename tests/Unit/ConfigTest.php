<?php

namespace Tests\Unit;

use PicPay\Prokers\Config;
use PicPay\Prokers\Exceptions\InvalidBrokerConfigurationException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private const CONFIG_BASE_PATH = '../data/';

    public function brokerProvider()
    {
        return [
            'rdkafka' => ['rdkafka'],
            'redis' => ['redis'],
            'beanstalkd' => ['beanstalkd'],
        ];
    }

    public function brokerAndQueueProvider()
    {
        return [
            'rdkafka and phpunit-test queue' => [
                'broker' => 'rdkafka',
                'queue' => 'phpunit-test',
            ],
            'rdkafka and another-phpunit-test queue' => [
                'broker' => 'rdkafka',
                'queue' => 'another-phpunit-test',
            ],
            'redis and phpunit-test queue' => [
                'broker' => 'redis',
                'queue' => 'phpunit-test',
            ],
            'redis and another-phpunit-test queue' => [
                'broker' => 'redis',
                'queue' => 'another-phpunit-test',
            ],
            'beanstalkd and phpunit-test queue' => [
                'broker' => 'beanstalkd',
                'queue' => 'phpunit-test',
            ],
            'beanstalkd and another-phpunit-test queue' => [
                'broker' => 'beanstalkd',
                'queue' => 'another-phpunit-test',
            ],
        ];
    }

    public function testGetConnectionDefaultShouldReturnDefaultKeyOnFile()
    {
        $enqueueConfig = $this->getConfigFromFile('enqueue');
        $config = new Config($enqueueConfig);

        $this->assertEquals(
            $enqueueConfig['default'],
            $config->getDefaultConnection()
        );
    }

    /**
     * @dataProvider brokerProvider
     */
    public function testGetShouldReturnDefaultBrokerConfiguration(string $broker)
    {
        $enqueueConfig = $this->getConfigFromFile('enqueue');
        $config = new Config($enqueueConfig);

        $this->assertEquals(
            $enqueueConfig['connections'][$broker],
            $config->get($broker),
            'Config mismatch for broker: ' . $broker
        );
    }

    /**
     * @dataProvider brokerProvider
     */
    public function testGetShouldReturnDefaultBrokerConfigurationUsingCommonStructure(string $broker)
    {
        $enqueueConfig = $this->getConfigFromFile('enqueue-using-common-structure');
        $config = new Config($enqueueConfig);

        $this->assertEquals(
            $enqueueConfig['connections'][$broker]['common'],
            $config->get($broker),
            'Config mismatch for broker: ' . $broker
        );
    }

    /**
     * @dataProvider brokerProvider
     */
    public function testGetShouldGetBrokerConfigurationWhenQueueConfigurationIsMissing(string $broker)
    {
        $enqueueConfig = $this->getConfigFromFile('enqueue-using-common-structure');
        $config = new Config($enqueueConfig);
        $queue = 'phpunit-test';

        $this->assertEquals(
            $enqueueConfig['connections'][$broker]['common'],
            $config->get($broker, $queue),
            sprintf(
                'Config mismatch for broker %s and queue %s',
                $broker,
                $queue
            )
        );
    }

    /**
     * @dataProvider brokerAndQueueProvider
     */
    public function testGetShouldReplaceBrokerConfigurationWithQueueConfiguration(
        string $broker,
        string $queue
    ) {
        $enqueueConfig = $this->getConfigFromFile('enqueue-overwritten-by-multiple-queues');
        $config = new Config($enqueueConfig);

        $expectedConfig = array_replace_recursive(
            $enqueueConfig['connections'][$broker]['common'],
            $enqueueConfig['connections'][$broker]['queues'][$queue]
        );

        $this->assertEquals(
            $expectedConfig,
            $config->get($broker, $queue),
            sprintf(
                'Config mismatch for broker %s and queue %s',
                $broker,
                $queue
            )
        );
    }

    public function testGetShouldThrowInvalidBrokerConfigurationException()
    {
        $enqueueConfig = $this->getConfigFromFile('enqueue');
        $config = new Config($enqueueConfig);

        $this->expectException(InvalidBrokerConfigurationException::class);
        $this->expectExceptionMessage(
            'Broker connection [invalid-broker] not configured.'
        );

        $config->get('invalid-broker');
    }

    public function testHasCustomConfigShouldReturnFalseForNonExistentQueueOnConfig()
    {
        $enqueueConfig = $this->getConfigFromFile('enqueue-overwritten-by-multiple-queues');
        $config = new Config($enqueueConfig);

        $this->assertFalse($config->hasCustomConfig('rdkakfa', 'non-existent-queue'));
    }

    public function testHasCustomConfigShouldReturnTrueForExistentQueueOnConfig()
    {
        $enqueueConfig = $this->getConfigFromFile('enqueue-overwritten-by-multiple-queues');
        $config = new Config($enqueueConfig);

        $this->assertFalse($config->hasCustomConfig('rdkakfa', 'phpunit-test'));
    }

    /**
     * Retrieves enqueue configuration.
     */
    private function getConfigFromFile(string $fileName): array
    {
        return require sprintf(
            '%s/%s/%s.php',
            __DIR__,
            self::CONFIG_BASE_PATH,
            $fileName
        );
    }
}
