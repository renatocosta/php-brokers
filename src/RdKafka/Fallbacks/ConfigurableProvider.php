<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

use SplDoublyLinkedList;

interface ConfigurableProvider
{

    /**
     * @return SplDoublyLinkedList
     */
    public function load(): SplDoublyLinkedList;
}
