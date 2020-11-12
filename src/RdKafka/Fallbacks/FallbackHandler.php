<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

interface FallbackHandler
{

    /**
     * @return void
     */
    public function handle(): void;
}
