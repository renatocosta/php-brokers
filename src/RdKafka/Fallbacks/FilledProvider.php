<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

interface FilledProvider
{

    /**
     * @return void
     */
    public function fill(): void;
}
