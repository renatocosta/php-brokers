<?php

namespace PicPay\Prokers\Extensions;

use Enqueue\Consumption\Context\ProcessorException;
use Enqueue\Consumption\ProcessorExceptionExtensionInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\Exception;

class RetryAndDlqExtension implements ProcessorExceptionExtensionInterface
{
    private $attempts;
    private $retryIntervalMs;
    private $dlq;

    public function __construct(int $attempts, int $retryIntervalMs, ?string $dlq)
    {
        $this->attempts = $attempts > 1 ? $attempts : 1;
        $this->retryIntervalMs = $retryIntervalMs;
        $this->dlq = $dlq;
    }

    public function onProcessorException(ProcessorException $handler): void
    {
        $message = $handler->getMessage();
        $currentAttempt = $message->getHeader('current_attempt', 1);

        if ($currentAttempt >= $this->attempts) {
            $this->upstreamToDlq($handler);
            return;
        }

        usleep($this->retryIntervalMs * 1000);
        $message->setHeader('current_attempt', ($currentAttempt + 1));
        $handler->setResult(Result::requeue('This message don\'t exceeded the attempts remaining'));
    }

    private function upstreamToDlq(ProcessorException $handler): void
    {
        $exception = $handler->getException();

        if (empty($this->dlq)) {
            $handler->getLogger()->info($exception->getMessage(), $exception->getTrace());
            $handler->setResult(Result::reject($exception->getMessage()));
            return;
        }

        $context = $handler->getContext();
        $message = $handler->getMessage();
        $message->setHeader('exception', $exception->getMessage());

        try {
            $context->createProducer()->send(
                $context->createTopic($this->dlq),
                $message
            );
        } catch (Exception $ex) {
            $handler->getLogger()->critical($ex->getMessage(), $ex->getTrace());
        }

        $report = sprintf('Message moved to %s, exception %s', $this->dlq, $exception->getMessage());
        $handler->getLogger()->info($report, $exception->getTrace());
        $handler->setResult(Result::reject($report));
    }
}
