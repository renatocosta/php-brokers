<?php

namespace PicPay\Prokers;

use PicPay\Prokers\Exceptions\InvalidBrokerConfigurationException;
use PicPay\Prokers\RdKafka\Fallbacks\FallbackBuilder;

class Config
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Retrieves config according to broker and queue given.
     *
     * @throws InvalidBrokerConfigurationException
     */
    public function get(string $broker, string $queue = ''): array
    {
        $connectionsConfig = $this->data['connections'] ?? [];

        if (!array_key_exists($broker, $connectionsConfig)) {
            throw new InvalidBrokerConfigurationException($broker);
        }

        return $this->mergeQueueConfig($connectionsConfig[$broker], $queue);
    }

    /**
     * Retrieves default connection defined on config file.
     */
    public function getDefaultConnection(): string
    {
        return $this->data['default'];
    }

    /**
     * Checks if exists a custom configuration for given queue.
     */
    public function hasCustomConfig(string $connection, string $queue): bool
    {
        $connectionsConfig = $this->data['connections'] ?? [];

        return isset($connectionsConfig[$connection]) &&
            isset($connectionsConfig[$connection]['queues'][$queue]);
    }

    /**
     * Merges common config and specific queue config.
     */
    protected function mergeQueueConfig(array $config, string $queue): array
    {
        if (false === isset($config['common'])) {
            return $config;
        }

        $common = $config['common'];

        if (false === isset($config['queues'][$queue])) {
            return $common;
        }

        return array_replace_recursive($common, $config['queues'][$queue]);
    }

    public function setConfigGroupId(array $config, string $groupId): array
    {
        $config['global']['group.id'] = $groupId;

        return $config;
    }
}
