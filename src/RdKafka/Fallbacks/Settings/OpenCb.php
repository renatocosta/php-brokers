<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Psr\Log\LoggerInterface;
use Closure;

final class OpenCb implements IdentifiedSettings
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
        return 'open_cb';
    }

    public function fallback(): Closure
    {
        /**
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#a467bb7b1ac070fee536227d6ae9cc551
         */
        return function ($pathname, $flags, $mode) {
            $this->logger->info($this->errCode(), [
                "pathname" => $pathname,
                "flags" => $flags,
                "mode" => $mode,
            ]);
        };
    }
}
