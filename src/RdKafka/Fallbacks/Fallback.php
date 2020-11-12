<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

final class Fallback implements FallbackHandler, OutcomeFallback
{

    /**
     *
     * @@var FallbackAggregator $fallbackProducer
     */
    private $fallbackProducer;

    /**
     * @var ConfigurableProvider
     */
    private $configurations;

    /**
     * @param ConfigurableProvider $configurations
     * @param FallbackAggregator $fallbackProducer
     */
    public function __construct(ConfigurableProvider $configurations, FallbackAggregator $fallbackProducer)
    {
        $this->configurations = $configurations->load();
        $this->fallbackProducer = $fallbackProducer;
    }

    public function handle(): void
    {

        foreach ($this->configurations as $configuration) {
            $this->fallbackProducer->register($configuration)
                ->append();
        }
    }

    public function output(): array
    {
        return $this->fallbackProducer->config();
    }
}
