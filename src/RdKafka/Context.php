<?php

namespace PicPay\Prokers\RdKafka;

use Enqueue\RdKafka\RdKafkaContext;
use PicPay\Prokers\RdKafka\Serializers\JsonSerializer;
use PicPay\Prokers\RdKafka\Serializers\Message;

/**
 * Class Context
 * @package PicPay\Prokers\RdKafka
 */
final class Context extends RdKafkaContext
{
    /**
     * Context extension for changing the serializer
     *
     * Context constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->setSerializer(new JsonSerializer());
    }

    public function createMessage(string $body = '', array $properties = [], array $headers = []): \Interop\Queue\Message
    {
        return new Message($body, $properties, $headers);
    }
}
