<?php

namespace Tests\Integration\Redis;

use Tests\Integration\TestCaseBase;

class ProducerTest extends TestCaseBase
{
    protected function setUp(): void
    {
        $this->broker = 'redis';
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

            $this->producer->connection("redis")->produce("teste2", $message);
            $messages[] = $message;
            $count++;
        }

        $this->consumer
            ->setConnection("redis")
            ->addExtension(new ExtensionTestBase())
            ->consume("teste2", new ProcessorTestBase($messages));

        $this->assertEquals($totalIteractions, $count, "publish and subscribe {$totalIteractions} messages");
    }

    public function testSetConnectionAndProduceMessage2()
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

            $this->producer->connection("redis")->produce("teste2", $message);
            $messages[] = $message;
            $count++;
        }

        $this->consumer
            ->setConnection("redis")
            ->addExtension(new ExtensionTestBase())
            ->consume("teste2", new ProcessorTestBase($messages));

        $this->assertEquals($totalIteractions, $count, "publish and subscribe {$totalIteractions} messages");
    }
}
