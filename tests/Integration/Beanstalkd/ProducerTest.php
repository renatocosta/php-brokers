<?php

namespace Tests\Integration\Beanstalkd;

use Tests\Integration\TestCaseBase;

class ProducerTest extends TestCaseBase
{
    protected function setUp(): void
    {
        $this->broker = 'beanstalkd';
        parent::setUp();
    }

    public function testProduceMessage()
    {
        $count = 0;
        $totalIteractions = 100;
        $messages = [];
        foreach (range(1, $totalIteractions) as $iteraction) {
            $message = [
                "time" => time(),
                "iteraction" => $iteraction,
                "totalIteractions" => $totalIteractions,
                "message" => sprintf("test_%s_%s", rand(1000, 9999), $iteraction)
            ];

            $this->producer->produce(self::TOPIC_NAME, $message);
            $messages[] = $message;
            $count++;
        }

        $this->consumer
            ->addExtension(new ExtensionTestBase())
            ->consume(self::TOPIC_NAME, new ProcessorTestBase($messages));

        $this->assertEquals($totalIteractions, $count, "publish and subscribe {$totalIteractions} messages");
    }

    public function testSetConnectionAndProduceMessage()
    {
        $count = 0;
        $totalIteractions = 100;
        $messages = [];
        foreach (range(1, $totalIteractions) as $iteraction) {
            $message = [
                "time" => time(),
                "iteraction" => $iteraction,
                "totalIteractions" => $totalIteractions,
                "message" => sprintf("test_%s_%s", rand(1000, 9999), $iteraction)
            ];

            $this->producer->connection("beanstalkd")->produce(self::TOPIC_NAME, $message);
            $messages[] = $message;
            $count++;
        }

        $this->consumer
            ->setConnection("beanstalkd")
            ->addExtension(new ExtensionTestBase())
            ->consume(self::TOPIC_NAME, new ProcessorTestBase($messages));

        $this->assertEquals($totalIteractions, $count, "publish and subscribe {$totalIteractions} messages");
    }
}
