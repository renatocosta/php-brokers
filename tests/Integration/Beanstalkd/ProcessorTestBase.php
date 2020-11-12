<?php

namespace Tests\Integration\Beanstalkd;

use Enqueue\Consumption\Result;
use Enqueue\Util\JSON;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;

class ProcessorTestBase implements Processor
{
    /**
     * @var array
     */
    private $messages;

    /**
     * ProcessorTestBase constructor.
     * @param array $messages
     */
    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    /**
     * @param Message $message
     * @param Context $context
     * @return object|string
     * @throws \Exception
     */
    public function process(Message $message, Context $context)
    {
        $data = JSON::decode($message->getBody());

        foreach ($this->messages as $key => $value) {
            if ($data['message'] == $value['message']) {
                unset($this->messages[$key]);
                return Result::ACK;
            }
        }

        throw new \Exception(sprintf("failed to obtain message, payload: [%s]", $message->getBody()));
    }
}
