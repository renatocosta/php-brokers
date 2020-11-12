<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

use PicPay\Prokers\RdKafka\Fallbacks\Settings\BackgroundEventCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\CloseSocketCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\ConnectCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\ConsumeCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\DrMsgCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\ErrorCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\LogCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\OpenCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\SocketCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\StatsCb;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\Throttle;
use Psr\Log\LoggerInterface;
use SplDoublyLinkedList;

final class ConfigurationProvider implements ConfigurableProvider, FilledProvider
{

    /**
     * @var SplDoublyLinkedList
     */
    private $list;

    /**
     * @var LoggerInterface
     */
    public $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->list = new SplDoublyLinkedList(SplDoublyLinkedList::IT_MODE_FIFO);
    }

    public function fill(): void
    {

        $this->list->push(new DrMsgCb($this->logger));
        $this->list->push(new LogCb($this->logger));
        $this->list->push(new ConnectCb($this->logger));
        $this->list->push(new SocketCb($this->logger));
        $this->list->push(new BackgroundEventCb($this->logger));
        $this->list->push(new OpenCb($this->logger));
        $this->list->push(new CloseSocketCb($this->logger));
        $this->list->push(new ConsumeCb($this->logger));
        $this->list->push(new StatsCb($this->logger));
        $this->list->push(new ErrorCb($this->logger));
        $this->list->push(new Throttle($this->logger));
    }

    public function load(): SplDoublyLinkedList
    {
        return $this->list;
    }
}
