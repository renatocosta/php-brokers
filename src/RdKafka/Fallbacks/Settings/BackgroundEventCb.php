<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Psr\Log\LoggerInterface;
use Closure;

final class BackgroundEventCb implements IdentifiedSettings
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
        return 'background_event_cb';
    }

    public function fallback(): Closure
    {
        /**
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#a5ce6c329ca692674b1c42460f9bab521
         */
        return function ($kafka, $event) {
            $this->logger->debug($this->errCode(), [
                "event" => $event
            ]);
        };
    }
}
