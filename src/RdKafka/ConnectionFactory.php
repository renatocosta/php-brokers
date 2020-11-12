<?php

namespace PicPay\Prokers\RdKafka;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Interop\Queue\Context;
use PicPay\Prokers\RdKafka\Context as RdKafkaContext;

/**
 * Class ConnectionFactory
 * @package PicPay\Prokers
 */
final class ConnectionFactory extends RdKafkaConnectionFactory
{
    /**
     * @var array
     */
    protected $config;

    /**
     * ConnectionFactory constructor.
     * @param string $config
     */
    public function __construct($config = 'kafka:')
    {
        $this->config = $config;
        parent::__construct($config);
    }

    /**
     * @return Context
     */
    public function createContext(): Context
    {
        return new RdKafkaContext($this->config);
    }
}
