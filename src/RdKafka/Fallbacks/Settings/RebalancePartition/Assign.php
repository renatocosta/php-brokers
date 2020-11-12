<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalancePartition;

use Psr\Log\LoggerInterface;

class Assign implements ReportedPartition
{
    private $kafka;
    private $partitions;

    public function collectIncident($kafka, $err, $partitions, LoggerInterface $logger): void
    {
        $this->kafka = $kafka;
        $this->partitions = $partitions;

        foreach ($this->partitions as $partition) {
            $logger->info('assigned partitions', [
                "topic" => $partition->getTopic(),
                "partition" => $partition->getPartition(),
                "offset" => $partition->getOffset(),
                "rebalance_code" => $err
            ]);
        }
    }

    public function assign(): void
    {
        $this->kafka->assign($this->partitions);
    }
}
