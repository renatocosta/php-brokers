<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Psr\Log\LoggerInterface;
use Closure;

final class ErrorCb implements IdentifiedSettings
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function errCode(): string
    {
        return 'error_cb';
    }

    public function fallback(): Closure
    {
        /**
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#ace721ef3b7c22d0c111ec747ef039a90
         */
        return function ($kafka, $err, $reason) {
            $this->logger->error($this->errCode(), [
                "err" => $err,
                "reason" => $reason
            ]);
        };
    }
}
