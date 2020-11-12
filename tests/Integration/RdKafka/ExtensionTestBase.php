<?php

namespace Tests\Integration\RdKafka;

use Enqueue\Consumption\Context\End;
use Enqueue\Consumption\Context\InitLogger;
use Enqueue\Consumption\Context\MessageReceived;
use Enqueue\Consumption\Context\MessageResult;
use Enqueue\Consumption\Context\PostConsume;
use Enqueue\Consumption\Context\PostMessageReceived;
use Enqueue\Consumption\Context\PreConsume;
use Enqueue\Consumption\Context\PreSubscribe;
use Enqueue\Consumption\Context\ProcessorException;
use Enqueue\Consumption\Context\Start;
use Enqueue\Consumption\ExtensionInterface;
use Enqueue\Util\JSON;

class ExtensionTestBase implements ExtensionInterface
{
    public function onEnd(End $context): void
    {
        // TODO: Implement onEnd() method.
    }

    public function onInitLogger(InitLogger $context): void
    {
        // TODO: Implement onInitLogger() method.
    }

    public function onMessageReceived(MessageReceived $context): void
    {
//        $data = JSON::decode($context->getMessage()->getBody());
//        printf($data . PHP_EOL);
    }

    public function onResult(MessageResult $context): void
    {
        // TODO: Implement onResult() method.
    }

    public function onPostConsume(PostConsume $context): void
    {
        // TODO: Implement onPostConsume() method.
    }

    public function onPostMessageReceived(PostMessageReceived $context): void
    {
        $data = JSON::decode($context->getMessage()->getBody());
        if (isset($data['iteraction']) && $data['iteraction'] == $data['totalIteractions']) {
            $context->interruptExecution(1);
        }
    }

    public function onPreConsume(PreConsume $context): void
    {
        // TODO: Implement onPreConsume() method.
    }

    public function onPreSubscribe(PreSubscribe $context): void
    {
        // TODO: Implement onPreSubscribe() method.
    }

    public function onProcessorException(ProcessorException $context): void
    {
        // TODO: Implement onProcessorException() method.
    }

    public function onStart(Start $context): void
    {
        // TODO: Implement onStart() method.
    }
}
