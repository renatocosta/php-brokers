<?php

namespace PicPay\Prokers\RdKafka\Fallbacks;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class FallbackBuilder
{
    /**
     * @var ConfigurableProvider
     */
    private $configurations;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function withDefaultCallbacks(): self
    {
        $this->configurations = new ConfigurationProvider($this->logger);
        $this->configurations->fill();
        return $this;
    }

    public function withRebalanceCallback(): self
    {
        $this->configurations = new RebalanceProvider($this->configurations);
        $this->configurations->fill();
        return $this;
    }

    public function build(): array
    {
        $fallbackConnection = new Fallback($this->configurations, new FallbackProducer());
        $fallbackConnection->handle();
        return $fallbackConnection->output();
    }

    public function withAllCallbacks(): self
    {
        $this->withDefaultCallbacks();
        $this->withRebalanceCallback();
        return $this;
    }
}
