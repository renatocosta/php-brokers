<?php

namespace PicPay\Prokers\RdKafka\Serializers;

use Enqueue\RdKafka\RdKafkaMessage;
use Enqueue\RdKafka\Serializer;
use InvalidArgumentException;

/**
 * Class RdkafkaMessageSerializer
 * @package PicPay\Prokers
 */
final class JsonSerializer implements Serializer
{
    public function toString(RdKafkaMessage $message): string
    {
        $body = json_decode($message->getBody(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(sprintf(
                'The malformed json given. Error %s and message %s',
                json_last_error(),
                json_last_error_msg()
            ));
        }

        $json = json_encode([
            'body' => $body,
            'properties' => $message->getProperties(),
            'headers' => $message->getHeaders(),
        ]);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(sprintf(
                'The malformed json given. Error %s and message %s',
                json_last_error(),
                json_last_error_msg()
            ));
        }

        return $json;
    }

    public function toMessage(string $string = null): RdKafkaMessage
    {
        $data = [];
        if ($string != null) {
            $data = json_decode($string, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                $message = sprintf(
                    'The malformed json given. Error %s and message %s',
                    json_last_error(),
                    json_last_error_msg()
                );

                return $this->buildMessage(
                    $string,
                    ["error" => $message],
                    []
                );
            }
        }

        if (!isset($data["body"])) {
            $data['body'] = $data;
        }

        if (!isset($data["properties"])) {
            $data['properties'] = [];
        }

        if (!isset($data["headers"])) {
            $data['headers'] = [];
        }

        if (is_array($data['body'])) {
            $data['body'] = json_encode($data['body']);
        }

        return $this->buildMessage($data['body'], $data['properties'], $data['headers']);
    }

    private function buildMessage($body, $properties, $headers)
    {
        return new Message($body, $properties, $headers);
    }
}
