<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Psr\Log\LoggerInterface;
use Closure;

final class SocketCb implements IdentifiedSettings
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
        return 'socket_cb';
    }

    public function fallback(): Closure
    {
        /**
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#a0467a6c20d5af69a29a63b530962ecbf
         */
        return function ($sockfd) {
            $this->logger->info($this->errCode(), [
                "sockfd" => $sockfd
            ]);
        };
    }
}
