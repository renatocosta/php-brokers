<?php

namespace PicPay\Prokers\Redis;

use Enqueue\Redis\RedisConnectionFactory;

/**
 * Class ConnectionFactory
 * @package PicPay\Prokers\Redis
 */
final class ConnectionFactory extends RedisConnectionFactory
{
    /**
     * @var array
     */
    protected $config;

    /**
     * ConnectionFactory constructor.
     * @param string $config
     */
    public function __construct($config = 'redis:')
    {
        $this->config = $config;
        parent::__construct($config);
    }
}
