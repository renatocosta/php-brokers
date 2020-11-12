<?php

namespace PicPay\Prokers\Contracts;

use Psr\Log\LoggerInterface;

interface LogHandlerInterface extends LoggerInterface
{
    /**
     * Updates log level of handler.
     *
     * @param int|string $level
     */
    public function setLogLevel($level): self;
}
