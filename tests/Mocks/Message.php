<?php

namespace Tests\Mocks;

use Interop\Queue\Impl\MessageTrait;

class Message implements \Interop\Queue\Message
{
    use MessageTrait;

    public function __construct(string $body = '', array $properties = [], array $headers = [])
    {
        $this->body = $body;
        $this->properties = $properties;
        $this->headers = $headers;

        $this->redelivered = false;
    }
}
