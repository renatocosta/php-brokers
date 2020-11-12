<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Psr\Log\LoggerInterface;
use Closure;

final class Throttle implements IdentifiedSettings
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
        return 'throttle';
    }

    public function fallback(): Closure
    {
        /**
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#a04160826ad039d42c10edec456163fa7
         */
        return function ($kafka, $brokerName, $brokerId, $throttleTimeMs) {
            $this->logger->info($this->errCode(), [
                "broker_name" => $brokerName,
                "broker_id" => $brokerId,
                "throttle_time_ms" => $throttleTimeMs,
            ]);
        };
    }
}
