<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

use PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalanceCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalancePartition\Assign;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalancePartition\Mismatch;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\RebalancePartition\Revoke;
use Psr\Log\LoggerInterface;
use SplDoublyLinkedList;

final class RebalanceProvider implements ConfigurableProvider, FilledProvider
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SplDoublyLinkedList
     */
    private $list;

    public const MISMATCH_IDENTIFIER = 282728891;

    public function __construct(ConfigurableProvider $configurationProvider)
    {
        $this->logger = $configurationProvider->logger;
        $this->list = $configurationProvider->load();
    }

    public function fill(): void
    {
        $rebalanceProcess = [
            -175 => new Assign(),
            -174 => new Revoke(),
            self::MISMATCH_IDENTIFIER => new Mismatch($this->logger),
        ];

        $this->list->push(new RebalanceCb($rebalanceProcess, $this->logger));
    }

    public function load(): SplDoublyLinkedList
    {
        return $this->list;
    }
}
