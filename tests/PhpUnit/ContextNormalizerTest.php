<?php

declare(strict_types=1);

namespace Phrity\Monolog\Test\PhpUnit;

use DateInterval;
use DateTime;
use DateTimeZone;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Phrity\Monolog\Processor\ContextNormalizer;
use Phrity\Monolog\Test\Fixture\{
    TestAttributeClass,
    TestClass,
    TestEnum,
    TestJsonSerializableClass,
    TestStringableClass,
};
use SplFileInfo;
use Symfony\Component\Uid\Uuid;

class ContextNormalizerTest extends TestCase
{
    public function testDefaultSerializer(): void
    {
        $logger = new Logger('test-default-serializer');
        $handler = new TestHandler();
        $logger->pushHandler($handler);
        $logger->pushProcessor(new ContextNormalizer());
        $logger->debug("test-default-serializer", [
            'string' => 'A string',
            'null' => null,
            'dateTime' => new DateTime('2025-04-11 16:23 UTC'),
            'dateTimeZone' => new DateTimeZone('UTC'),
            'dateInterval' => new DateInterval('P1Y'),
            'dataUri' => new SplFileInfo(__FILE__),
            'uid' => Uuid::v4(),
            'testEnum' => TestEnum::Yes,
            'testClass' => new TestClass(),
            'testStringableClass' => new TestStringableClass(),
            'testJsonSerializableClass' => new TestJsonSerializableClass(),
            'testAttributeClass' => new TestAttributeClass(),
        ]);

        $logContext = $handler->getRecords()[0]->context;

        $this->assertEquals('A string', $logContext['string']);
        $this->assertEquals(null, $logContext['null']);
        $this->assertEquals('2025-04-11T16:23:00+00:00', $logContext['dateTime']);
        $this->assertEquals('UTC', $logContext['dateTimeZone']);
        $this->assertEquals('P1Y0M0DT0H0M0S', $logContext['dateInterval']);
        $this->assertStringStartsWith('data:application/octet-stream;base64', $logContext['dataUri']);
        $this->assertIsString($logContext['uid']);
        $this->assertEquals('yes', $logContext['testEnum']);
        $this->assertEquals(['public' => 'public'], $logContext['testClass']);
        $this->assertEquals(['public' => 'public-stringable'], $logContext['testStringableClass']);
        $this->assertEquals([
            'public' => 'public-json-serializeble',
            'private' => 'private-json-serializeble',
            'added' => 'added'
        ], $logContext['testJsonSerializableClass']);
        $this->assertEquals(['dateTime' => '2025.04.11'], $logContext['testAttributeClass']);
    }
}
