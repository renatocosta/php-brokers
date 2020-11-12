<?php

namespace PicPay\Prokers\Exceptions;

use InvalidArgumentException;

class InvalidBrokerConfigurationException extends InvalidArgumentException
{
    /**
     * Constructor.
     */
    public function __construct(string $broker)
    {
        parent::__construct(sprintf(
            'Broker connection [%s] not configured.',
            $broker
        ));
    }
}
