<?php

namespace Tests\Integration\RdKafka;

use Enqueue\Util\UUID;
use Tests\Integration\TestCaseBase;

class ProducerTest extends TestCaseBase
{
    protected function setUp(): void
    {
        $this->broker = 'rdkafka';
        parent::setUp();
    }
    
    public function testProduceMessage()
    {
        $topic = uniqid('test_prokers_');
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

            $this->producer->produce($topic, $message);
            $messages[] = $message;
            $count++;
        }

        $this->consumer
            ->addExtension(new ExtensionTestBase())
            ->consume($topic, new ProcessorTestBase($messages));

        $this->assertEquals($totalIteractions, $count, "publish and subscribe {$totalIteractions} messages");
    }

    public function testSetConnectionAndProduceMessage()
    {
        $topic = uniqid('test_prokers_');
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

            $this->producer->connection("rdkafka")->produce($topic, $message);
            $messages[] = $message;
            $count++;
        }

        $this->consumer
            ->setConnection("rdkafka")
            ->addExtension(new ExtensionTestBase())
            ->consume($topic, new ProcessorTestBase($messages));

        $this->assertEquals($totalIteractions, $count, "publish and subscribe {$totalIteractions} messages");
    }

    public function testSetConnectionAndProduceMessage2()
    {
        $topic = uniqid('test_prokers_');
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

            $this->producer->connection("rdkafka")->produce($topic, $message);
            $messages[] = $message;
            $count++;
        }

        $this->consumer
            ->setConnection("rdkafka")
            ->addExtension(new ExtensionTestBase())
            ->consume($topic, new ProcessorTestBase($messages));

        $this->assertEquals($totalIteractions, $count, "publish and subscribe {$totalIteractions} messages");
    }

    public function testProduceMessageWithCorrelationId()
    {
        $topic = uniqid('test_prokers_');
        $correlationId = UUID::generate();
        $payload = [
            'test' => 'correlation id',
            'iteraction' => 1,
            'totalIteractions' => 1
        ];

        $this->producer->setCorrelationId($correlationId);
        $this->producer->connection("rdkafka")->produce($topic, $payload);

        $this->consumer
            ->setConnection("rdkafka")
            ->addExtension(new ExtensionTestBase())
            ->consume($topic, new CorrelationIdProcessor($correlationId));

        $this->assertTrue(true);
    }
}
