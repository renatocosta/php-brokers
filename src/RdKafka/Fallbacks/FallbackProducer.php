<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

final class FallbackProducer extends FallbackAggregator
{

    public function append(): void
    {
        $this->appendFrom($this->upcomingSetting->errCode(), $this->upcomingSetting->fallback());
    }
}
