<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use PicPay\Prokers\RdKafka\Fallbacks\RebalanceProvider;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalancePartition\ReportedPartition;
use Psr\Log\LoggerInterface;
use Closure;

final class RebalanceCb implements IdentifiedSettings
{

    /**
     * @var ReportedPartition[]
     */
    private $providers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(array $providers, LoggerInterface $logger)
    {
        $this->providers = $providers;
        $this->logger = $logger;
    }

    public function errCode(): string
    {
        return 'rebalance_cb';
    }

    public function fallback(): Closure
    {

        /**s
         * @link https://docs.confluent.io/current/clients/librdkafka/rdkafka_8h.html#a10db731dc1a295bd9884e4f8cb199311
         */
        return function ($kafka, $err, $partitions) {

            //Check if err code is a mismatch provider. Then set it as a MISMATCH_IDENTIFIER
            if (!array_key_exists($err, $this->providers)) {
                $err = RebalanceProvider::MISMATCH_IDENTIFIER;
            }

            $provider = $this->providers[$err];
            $provider->collectIncident($kafka, $err, $partitions, $this->logger);
            $provider->assign();
        };
    }
}
