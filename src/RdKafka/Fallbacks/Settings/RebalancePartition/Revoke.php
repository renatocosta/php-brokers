<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalancePartition;

use Psr\Log\LoggerInterface;

class Revoke implements ReportedPartition
{
    private $kafka;

    public function collectIncident($kafka, $err, $partitions, LoggerInterface $logger): void
    {
        $this->kafka = $kafka;

        foreach ($partitions as $partition) {
            $logger->info('revoked partitions', [
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
