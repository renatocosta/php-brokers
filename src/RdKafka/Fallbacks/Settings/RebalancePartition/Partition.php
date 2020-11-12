<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalancePartition;

interface Partition
{

    /**
     * Assign some partitions
     */
    public function assign(): void;
}
