<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Psr\Log\LoggerInterface;
use Closure;

final class LogCb implements IdentifiedSettings
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
        return 'log_cb';
    }

    public function fallback(): Closure
    {
        /**
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#a06ade2ca41f32eb82c6f7e3d4acbe19f
         */
        return function ($kafka, $level, $facility, $message) {
            $this->logger->info($this->errCode(), [
                "level" => $level,
                "facility" => $facility,
                "message" => $message,
            ]);
        };
    }
}
