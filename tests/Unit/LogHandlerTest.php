<?php

namespace Tests\Unit;

use Mockery as m;
use PicPay\Prokers\LogHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LogHandlerTest extends TestCase
{
    /**
     * @var array
     */
    private $levels = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    ];

    public function logLevelAsStringProvider()
    {
        return [
            'emergency' => [LogLevel::EMERGENCY],
            'alert' => [LogLevel::ALERT],
            'critical' => [LogLevel::CRITICAL],
            'error' => [LogLevel::ERROR],
            'warning' => [LogLevel::WARNING],
            'notice' => [LogLevel::NOTICE],
            'info' => [LogLevel::INFO],
            'debug' => [LogLevel::DEBUG],
        ];
    }

    public function logLevelAsIntegerProvider()
    {
        return [
            'emergency' => [0, LogLevel::EMERGENCY],
            'alert' => [1, LogLevel::ALERT],
            'critical' => [2, LogLevel::CRITICAL],
            'error' => [3, LogLevel::ERROR],
            'warning' => [4, LogLevel::WARNING],
            'notice' => [5, LogLevel::NOTICE],
            'info' => [6,LogLevel::INFO],
            'debug' => [7, LogLevel::DEBUG],
        ];
    }

    public function invalidLevelProvider()
    {
        return [
            'greater than 7' => [rand(8, 20)],
            'less than 0' => [rand(-20, -1)],
            'invalid string' => ['invalid level'],
            'invalid case' => ['DEBUG'],
            'nullable' => [null],
        ];
    }

    /**
     * @dataProvider logLevelAsStringProvider
     */
    public function testLogShouldRedirectCallsToLoggerClass(string $level)
    {
        $logger = m::mock(LoggerInterface::class);
        $handler = new LogHandler($logger);
        $handler->setLogLevel(LogLevel::DEBUG);

        $message = 'some message';
        $context = ['some', 'context'];

        $logger->shouldReceive('log')
            ->with($level, $message, $context)
            ->once();

        $handler->log($level, $message, $context);
    }

    /**
     * @dataProvider logLevelAsStringProvider
     */
    public function testSetValidLogLevelAsStringShouldUpdateAttribute(string $level)
    {
        $handler = new LogHandler(m::mock(LoggerInterface::class));
        $handler->setLogLevel($level);

        $this->assertAttributeEquals($level, 'level', $handler);
    }

    /**
     * @dataProvider logLevelAsIntegerProvider
     */
    public function testSetValidLogLevelAsIntegerShouldUpdateAttribute(int $level, string $levelAsString)
    {
        $handler = new LogHandler(m::mock(LoggerInterface::class));
        $handler->setLogLevel($level);

        $this->assertAttributeEquals($levelAsString, 'level', $handler);
    }

    /**
     * @dataProvider invalidLevelProvider
     */
    public function testSetInvalidLogLevelShouldAssumeEmeergencyOnly($invalidLevel)
    {
        $handler = new LogHandler(m::mock(LoggerInterface::class));
        $handler->setLogLevel($invalidLevel);

        $this->assertAttributeEquals(LogLevel::EMERGENCY, 'level', $handler);
    }

    /**
     * @dataProvider logLevelAsStringProvider
     */
    public function testLogShouldIgnoreMessagesWithIncompatibleLevel(string $handlerLevel)
    {
        $logger = m::mock(LoggerInterface::class);
        $handler = new LogHandler($logger);
        $handler->setLogLevel($handlerLevel);
        $message = 'some message';
        $context = ['some', 'context'];

        foreach ($this->levels as $level) {
            $maxLevel = array_search($handlerLevel, $this->levels);
            $currentLevel = array_search($level, $this->levels);
            $timesToCall = (int) ($currentLevel <= $maxLevel);

            $logger->shouldReceive('log')
                ->with($level, $message, $context)
                ->times($timesToCall);

            $handler->log($level, $message, $context);
        }
    }
}
