<?php

namespace PicPay\Prokers\Extensions;

use Enqueue\Consumption\Context\End;
use Enqueue\Consumption\Context\MessageReceived;
use Enqueue\Consumption\Context\MessageResult;
use Enqueue\Consumption\Context\ProcessorException;
use Enqueue\Consumption\EndExtensionInterface;
use Enqueue\Consumption\MessageReceivedExtensionInterface;
use Enqueue\Consumption\MessageResultExtensionInterface;
use Enqueue\Consumption\ProcessorExceptionExtensionInterface;
use Enqueue\Consumption\Result;

class NewRelicExtension implements
    MessageReceivedExtensionInterface,
    MessageResultExtensionInterface,
    ProcessorExceptionExtensionInterface,
    EndExtensionInterface
{
    /**
     * @var string
     */
    private $parameterPrefix = 'prokers.';

    /**
     * @var int
     */
    private $startAtMs;

    private function getNowMs()
    {
        return round(microtime(true) * 1000);
    }

    private function getMemoryUsageMb()
    {
        return memory_get_usage(true) / 1024 / 1024;
    }

    private function getCpuUsagePercent(): float
    {
        return sys_getloadavg()[0];
    }

    private function formatParameter(string $string): string
    {
        return $this->parameterPrefix . $string;
    }

    /**
     * @var Result|object|string|null
     */
    private function formatResult($result): string
    {
        if ($result instanceof Result) {
            return $result->getStatus();
        }

        if (is_string($result)) {
            return $result;
        }

        return 'N/A';
    }

    /**
     * @inheritDoc
     */
    public function onEnd(End $context): void
    {
        newrelic_end_transaction();
    }

    /**
     * @inheritDoc
     */
    public function onMessageReceived(MessageReceived $context): void
    {
        newrelic_add_custom_parameter(
            $this->formatParameter('topic'),
            $context->getConsumer()->getQueue()->getQueueName()
        );

        $correlationId = $context->getMessage()->getCorrelationId();
        if (!empty($correlationId)) {
            newrelic_add_custom_parameter($this->formatParameter('correlation-id'), $correlationId);
            newrelic_add_custom_parameter('X-Request-ID', $correlationId);
        }

        $this->startAtMs = $this->getNowMs();
    }

    /**
     * @inheritDoc
     */
    public function onResult(MessageResult $context): void
    {
        newrelic_add_custom_parameter(
            $this->formatParameter('cpu'),
            sprintf('%.2F%%', $this->getCpuUsagePercent())
        );
        newrelic_add_custom_parameter(
            $this->formatParameter('memory'),
            sprintf('%.2F Mb', $this->getMemoryUsageMb())
        );
        newrelic_add_custom_parameter(
            $this->formatParameter('message_status'),
            $this->formatResult($context->getResult())
        );
        newrelic_add_custom_parameter(
            $this->formatParameter('time_to_proccess'),
            sprintf('%dms', $this->getNowMs() - $this->startAtMs)
        );
    }

    /**
     * @inheritDoc
     */
    public function onProcessorException(ProcessorException $context): void
    {
        newrelic_notice_error($context->getException()->getMessage(), $context->getException());
    }
}
