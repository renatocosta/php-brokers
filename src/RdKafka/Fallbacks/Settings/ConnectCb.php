<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Psr\Log\LoggerInterface;
use Closure;

final class ConnectCb implements IdentifiedSettings
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
        return 'connect_cb';
    }

    public function fallback(): Closure
    {
        /**
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#a53dd1b77019324170d0168617fdaf040
         */
        return function ($sockfd, $addr, $addrlen, $id) {
            $this->logger->info($this->errCode(), [
                "sockfd" => $sockfd,
                "addr" => $addr,
                "addrlen" => $addrlen,
                "id" => $id,
            ]);
        };
    }
}
