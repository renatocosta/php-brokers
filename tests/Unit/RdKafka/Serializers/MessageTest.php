<?php

namespace Tests\Unit\RdKafka\Serializers;

use Enqueue\Util\UUID;
use PHPUnit\Framework\TestCase;
use PicPay\Prokers\RdKafka\Serializers\Message;

class MessageTest extends TestCase
{
    public function testMessageWithCorrelationId()
    {
        $correlationId = UUID::generate();

        $message = new Message();
        $message->setCorrelationId($correlationId);

        $this->assertArrayHasKey('correlationId', $message->getHeaders());
        $this->assertEquals($correlationId, $message->getCorrelationId());
        $this->assertEquals($correlationId, $message->getHeader('correlationId'));
    }

    public function testMessageWithoutCorrelationId()
    {
        $message = new Message();

        $this->assertNull($message->getHeader('correlationId'));
        $this->assertNull($message->getCorrelationId());
        $this->assertArrayNotHasKey('correlationId', $message->getHeaders());
    }
}