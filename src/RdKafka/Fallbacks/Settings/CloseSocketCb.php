<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Psr\Log\LoggerInterface;
use Closure;

final class CloseSocketCb implements IdentifiedSettings
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
        return 'closesocket_cb';
    }

    public function fallback(): Closure
    {
        /**
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#ab55c7ddc46a354e3f57b5b209e5ec3c7
         */
        return function ($sockfd) {
            $this->logger->info($this->errCode(), [
                "sockfd" => $sockfd
            ]);
        };
    }
}
