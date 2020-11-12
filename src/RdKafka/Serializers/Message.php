<?php

namespace PicPay\Prokers\RdKafka\Serializers;

use Enqueue\RdKafka\RdKafkaMessage;
use PicPay\Prokers\Traits\CorrelationIdTrait;

/**
 * Class Message
 * @package PicPay\Prokers\Serializers
 */
final class Message extends RdKafkaMessage
{
    use CorrelationIdTrait;
}
