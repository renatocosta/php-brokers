<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalancePartition;

use Psr\Log\LoggerInterface;

class Mismatch implements ReportedPartition
{
    private $kafka;

    public function collectIncident($kafka, $err, $partitions, LoggerInterface $logger): void
    {
        $this->kafka = $kafka;

        foreach ($partitions as $partition) {
            $logger->info('topic rebalance', [
                "topic" => $partition->getTopic(),
                "partition" => $partition->getPartition(),
                "offset" => $partition->getOffset(),
                "rebalance_code" => $err
            ]);
        }
    }

    public function assign(): void
    {
        $this->kafka->assign(null);
    }
}
