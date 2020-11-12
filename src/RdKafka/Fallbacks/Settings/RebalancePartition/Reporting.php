<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalancePartition;

use Psr\Log\LoggerInterface;

interface Reporting
{

    /**
     * @param $kafka
     * @param $err
     * @param $partitions
     * @param LoggerInterface $logger
     */
    public function collectIncident($kafka, $err, $partitions, LoggerInterface $logger): void;
}
