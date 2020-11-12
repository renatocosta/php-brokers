<?php

namespace PicPay\Prokers\RdKafka\Fallbacks\Settings;

use Closure;

interface IdentifiedSettings
{
    public function errCode(): string;

    public function fallback(): Closure;
}
