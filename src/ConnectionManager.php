<?php

namespace PicPay\Prokers;

use Exception;
use Interop\Queue\ConnectionFactory;
use Interop\Queue\Context;
use Interop\Queue\Producer;
use InvalidArgumentException;
use PicPay\Prokers\Exceptions\InvalidBrokerConfigurationException;
use PicPay\Prokers\RdKafka\Fallbacks\FallbackBuilder;
use PicPay\Prokers\Contracts\ConnectionManagerInterface;
use PicPay\Prokers\Contracts\LogHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class ConnectionManager
 *
 * @package PicPay\Prokers
 */
final class ConnectionManager implements ConnectionManagerInterface
{
    /**
     * Brokers configurations.
     *
     * @var Config $config
     */
    protected $config;

    /**
     * The broker context.
     *
     * @var Context
     */
    private $context;

    /**
     * The broker producer.
     *
     * @var Producer
     */
    private $producer;

    /**
     * The active context instances.
     *
     * @var array
     */
    protected $contexts = [];

    /**
     * Current connection identifier.
     *
     * @var string
     */
    protected $connection;

    /**
     * The custom connections.
     *
     * @var array
     */
    protected $extensions = [
        'rdkafka' => \PicPay\Prokers\RdKafka\ConnectionFactory::class,
        'kafka' => \PicPay\Prokers\RdKafka\ConnectionFactory::class,
        'beanstalkd' => \Enqueue\Pheanstalk\PheanstalkConnectionFactory::class,
        'redis' => \Enqueue\Redis\RedisConnectionFactory::class,
    ];

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * The group.id
     *
     * @var string
     */
    protected $groupId;

    /**
     * Create a new broker manager instance.
     *
     * @param Config $config
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->connection = $this->getDefaultConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function setConnection(?string $name): ConnectionManagerInterface
    {
        $connectionName = $name ?? $this->getDefaultConnection();
        
        if (!array_key_exists($connectionName, $this->extensions)) {
            throw new InvalidBrokerConfigurationException($connectionName);
        }

        $this->connection = $connectionName;

        return $this;
    }

    /**
     * Initialize logger
     *
     * @param LoggerInterface|null $logger
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = new LogHandler($logger);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LogHandlerInterface
    {
        return $this->logger ?: new LogHandler(new NullLogger());
    }

    /**
     * Get connection context.
     *
     * @return Context
     * @throws Exception
     */
    public function getContext(string $queue = ''): Context
    {
        $contextKey = $this->connection;

        if ($this->config->hasCustomConfig($this->connection, $queue)) {
            $contextKey .= $queue;
        }

        $contextConfig = $this->config->get($this->connection, $queue);

        if (isset($contextConfig['log_level'])) {
            $this->getLogger()->setLogLevel($contextConfig['log_level']);
        }

        if (!empty($this->getGroupId())) {
            $contextConfig = $this->config->setConfigGroupId($contextConfig, $this->getGroupId());
        }

        $this->contexts[$contextKey] = $this->createContext($contextConfig);

        return $this->contexts[$contextKey];
    }

    /**
     * @return Producer
     */
    public function getProducer(string $queue = ''): Producer
    {
        $this->producer = $this->getContext($queue)->createProducer();

        return $this->producer;
    }

    /**
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->config->getDefaultConnection();
    }

    /**
     * Make the broker context instance.
     *
     * @param array $contextConfig
     * @return Context
     */
    protected function createContext(array $contextConfig): Context
    {
        if (in_array($this->connection, ['rdkafka', 'kafka'])) {
            $contextConfig = $this->addLogCallbacks($contextConfig);
        }

        $extensionClass = $this->extensions[$this->connection];
        $extension = new $extensionClass($contextConfig);

        return $extension->createContext();
    }

    /**
     * Adds callback functions to log Kafka interations.
     */
    private function addLogCallbacks(array $config): array
    {
        $fallback = (new FallbackBuilder($this->getLogger()))
            ->withAllCallbacks()
            ->build();

        return array_merge($fallback, $config);
    }

    public function setGroupId(?string $groupId): ConnectionManagerInterface
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function getGroupId(): ?string
    {
        return $this->groupId;
    }
}
