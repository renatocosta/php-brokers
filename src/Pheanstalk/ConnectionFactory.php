<?php

namespace PicPay\Prokers\Pheanstalk;

use Enqueue\Pheanstalk\PheanstalkConnectionFactory;

/**
 * Class ConnectionFactory
 *
 * @package PicPay\Prokers\Pheanstalk
 */
final class ConnectionFactory extends PheanstalkConnectionFactory
{
    /**
     * @var array
     */
    protected $config;

    /**
     * ConnectionFactory constructor.
     * @param string $config
     */
    public function __construct($config = 'beanstalk:')
    {
        $this->config = $config;
        parent::__construct($config);
    }
}
