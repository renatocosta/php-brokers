<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

use PicPay\Prokers\RdKafka\Fallbacks\Settings\EndFallback;
use PicPay\Prokers\RdKafka\Fallbacks\Settings\IdentifiedSettings;
use Closure;

abstract class FallbackAggregator
{

    /**
     * @var IdentifiedSettings
     */
    protected $upcomingSetting;

    /**
     * @var arrays
     */
    protected $configs = [];

    /**
     * Register upcoming setting.
     * @param IdentifiedSettings $upcomingSetting
     * @return FallbackAggregator
     */
    public function register(IdentifiedSettings $upcomingSetting): FallbackAggregator
    {
        $this->upcomingSetting = $upcomingSetting;
        return $this;
    }

    /**
     *
     * @return void
     */
    abstract public function append(): void;

    /**
     * @param string $identifier
     * @param Closure $function
     */
    protected function appendFrom(string $identifier, Closure $function): void
    {
        $newConfig = [$identifier => $function];
        $this->configs = array_merge($this->configs, $newConfig);
    }

    /**
     * The outcome fallbacks configuration list
     * @return array
     */
    public function config(): array
    {
        return $this->configs;
    }
}
