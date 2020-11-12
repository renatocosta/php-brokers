<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Psr\Log\LoggerInterface;
use Closure;

final class StatsCb implements IdentifiedSettings
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
        return 'stats_cb';
    }

    public function fallback(): Closure
    {
        /**
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#a597d00432e3ca22174d18e7e348fb766
         */
        return function ($message) {
            $this->logger->info($this->errCode(), [
                "message" => $message
            ]);
        };
    }
}
