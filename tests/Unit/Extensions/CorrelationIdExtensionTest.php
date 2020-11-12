<?php

namespace Tests\Unit\Extensions;

use Enqueue\Consumption\Context\MessageReceived;
use Enqueue\Util\UUID;
use Interop\Queue\Consumer;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use Mockery;
use PHPUnit\Framework\TestCase;
use PicPay\Prokers\Extensions\CorrelationIdExtension;
use PicPay\Prokers\RdKafka\Serializers\Message as TestMessage;
use Psr\Log\LoggerInterface;
use Tests\Builder\ContextBuilder;
use Tests\Mocks\CorrelationIdStorage;

class CorrelationIdExtensionTest extends TestCase
{
    public function testSaveCorrelationIdInStorage()
    {
        $storage = new CorrelationIdStorage();
        $extension = new CorrelationIdExtension($storage);

        for ($i = 1; $i <= 5; $i++) {
            $uuid = ($i % 2) === 0 ? null : UUID::generate();
            $message = new TestMessage();
            $message->setCorrelationId($uuid);

            $extension->onMessageReceived(
                $this->buildHander($message)
            );

            $this->assertEquals($uuid, $storage->getCorrelationId());
        }
    }

    private function buildHander(Message $message)
    {
        return new MessageReceived(
            (new ContextBuilder())->build(),
            Mockery::mock(Consumer::class),
            $message,
            Mockery::mock(Processor::class),
            time(),
            Mockery::mock(LoggerInterface::class)
        );
    }
}
