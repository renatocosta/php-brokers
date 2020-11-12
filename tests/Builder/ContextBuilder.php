<?php

namespace Tests\Builder;

use Interop\Queue\Context;
use Interop\Queue\Exception\Exception;
use Interop\Queue\Producer;
use Interop\Queue\Topic;

class ContextBuilder
{
    /**
     * @var Context|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    private $context;

    public function __construct()
    {
        $this->context = \Mockery::mock(Context::class);
    }

    public function withTopic(): self
    {
        $this->context->shouldReceive('createTopic')->andReturn(\Mockery::mock(Topic::class));
        return $this;
    }

    public function withSuccessProducer(): self
    {
        $producer = \Mockery::mock(Producer::class);
        $producer->shouldReceive('send')->andReturn(null);
        $this->context->shouldReceive('createProducer')->andReturn($producer);
        return $this;
    }

    public function withErrorProducer(\Exception $exception = null): self
    {
        if (!$exception instanceof Exception) {
            $exception = new Exception('Mock exception for producer');
        }

        $producer = \Mockery::mock(Producer::class);
        $producer->shouldReceive('send')->andThrow($exception);
        $this->context->shouldReceive('createProducer')->andReturn($producer);
        return $this;
    }

    public function build()
    {
        return $this->context;
    }
}
