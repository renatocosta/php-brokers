<?php

namespace Tests\Unit\Extensions;

use PicPay\Prokers\Extensions\RetryAndDlqExtension;
use Tests\Builder\ContextBuilder;
use Enqueue\Consumption\Context\ProcessorException;
use Tests\Mocks\Logger;
use PHPUnit\Framework\TestCase;
use Tests\Mocks\Message;
use Interop\Queue\Consumer;

class RetryAndDlqExtensionTest extends TestCase
{
    private $message;

    protected function setUp()
    {
        parent::setUp();
        $this->message = new Message();
    }

    public function testWithoutRetry()
    {
        $handler = $this->mockHandler();
        $extension = $this->buildExtension(1, 10, null);
        $extension->onProcessorException($handler);

        $this->assertFalse(array_key_exists('current_attempt', $this->message->getHeaders()));
        $this->assertFalse(array_key_exists('exception', $this->message->getHeaders()));
    }

    public function testWithRetryAndWithoutDlq()
    {
        $handler = $this->mockHandler();
        $extension = $this->buildExtension(2, 5, null);
        $extension->onProcessorException($handler);

        $this->assertTrue(array_key_exists('current_attempt', $this->message->getHeaders()));
        $this->assertFalse(array_key_exists('exception', $this->message->getHeaders()));
        $this->assertEquals(2, $this->message->getHeader('current_attempt'));
    }

    public function testSendToDlqWithoutRetry()
    {
        $handler = $this->mockHandler();
        $extension = $this->buildExtension(1, 10, 'dlq');
        $extension->onProcessorException($handler);

        $this->assertTrue(array_key_exists('exception', $this->message->getHeaders()));
        $this->assertEquals('Unit test', $this->message->getHeader('exception'));
    }

    public function testSendToDlqWithRetry()
    {
        $this->message->setHeader('current_attempt', 4);

        $handler = $this->mockHandler();
        $extension = $this->buildExtension(3, 2, 'dlq');
        $extension->onProcessorException($handler);

        $this->assertTrue(array_key_exists('exception', $this->message->getHeaders()));
        $this->assertEquals('Unit test', $this->message->getHeader('exception'));
        $this->assertEquals(4, $this->message->getHeader('current_attempt'));
    }

    public function testExceptionToSendToDlq()
    {
        $this->message->setHeader('current_attempt', 4);

        $context = (new ContextBuilder())->withTopic()->withErrorProducer()->build();
        $handler = $this->mockHandler($context);
        $extension = $this->buildExtension(3, 2, 'dlq');
        $extension->onProcessorException($handler);

        $this->assertTrue(array_key_exists('exception', $this->message->getHeaders()));
        $this->assertEquals('Unit test', $this->message->getHeader('exception'));
        $this->assertEquals(4, $this->message->getHeader('current_attempt'));
    }

    private function mockHandler($context = null)
    {
        $context = $context ?? (new ContextBuilder())->withTopic()->withSuccessProducer()->build();

        return new ProcessorException(
            $context,
            \Mockery::mock(Consumer::class),
            $this->message,
            new \Exception('Unit test'),
            time(),
            new Logger()
        );
    }

    private function buildExtension($attempts, $retryIntervalMs, $dlq)
    {
        return new RetryAndDlqExtension($attempts, $retryIntervalMs, $dlq);
    }
}
