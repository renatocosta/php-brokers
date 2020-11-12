<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

interface OutcomeFallback
{

    /**
     * @return array
     */
    public function output(): array;
}
