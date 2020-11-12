<?php

namespace PicPay\Prokers;

use PicPay\Prokers\Contracts\LogHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

class LogHandler implements LogHandlerInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected $level = LogLevel::EMERGENCY;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    private $levels = [
        0 => LogLevel::EMERGENCY,
        1 => LogLevel::ALERT,
        2 => LogLevel::CRITICAL,
        3 => LogLevel::ERROR,
        4 => LogLevel::WARNING,
        5 => LogLevel::NOTICE,
        6 => LogLevel::INFO,
        7 => LogLevel::DEBUG,
    ];

    /**
     * Construtor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogLevel($level): LogHandlerInterface
    {
        if (array_key_exists($level, $this->levels)) {
            $level = $this->levels[$level];
        }

        if (false === array_search($level, $this->levels)) {
            $level = LogLevel::EMERGENCY;
        }

        $this->level = $level;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        $maxLevel = array_search($this->level, $this->levels);
        $levelIndex = array_search($level, $this->levels);

        if ($levelIndex > $maxLevel) {
            return;
        }

        $this->logger->log($level, $message, $context);
    }
}
