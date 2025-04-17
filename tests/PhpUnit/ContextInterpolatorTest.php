<?php

declare(strict_types=1);

namespace Phrity\Monolog\Test\PhpUnit;

use DateInterval;
use DateTime;
use DateTimeZone;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Phrity\Monolog\Processor\ContextInterpolator;
use Phrity\Monolog\Tools\DefaultSerializer;
use Phrity\Monolog\Test\Fixture\{
    TestAttributeClass,
    TestClass,
    TestEnum,
    TestJsonSerializableClass,
    TestStringableClass,
};
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Uid\Uuid;

class ContextInterpolatorTest extends TestCase
{
    public function testDefaultInterpolator(): void
    {
        $logger = new Logger('test-default-interpolator');
        $handler = new TestHandler();
        $logger->pushHandler($handler);
        $logger->pushProcessor(new ContextInterpolator());

        $logger->debug("string: {string}", [
            'string' => 'A string'
        ]);
        $logger->debug("null: {null}", [
            'null' => null
        ]);
        $logger->debug("dateTime: {dateTime}", [
            'dateTime' => new DateTime('2025-04-11 16:23 UTC')
        ]);
        $logger->debug("dateTimeZone: {dateTimeZone}", [
            'dateTimeZone' => new DateTimeZone('UTC')
        ]);
        $logger->debug("dateInterval: {dateInterval}", [
            'dateInterval' => new DateInterval('P1Y')
        ]);
        $logger->debug("dataUri: {dataUri}", [
            'dataUri' => new SplFileInfo(__FILE__)
        ]);
        $logger->debug("uuid: {uuid}", [
            'uuid' => Uuid::v3(Uuid::fromString(Uuid::NAMESPACE_OID), 'test-default-interpolator')
        ]);
        $logger->debug("testEnum: {testEnum}", [
            'testEnum' => TestEnum::Yes
        ]);
        $logger->debug("testClass: {testClass}", [
            'testClass' => new TestClass()
        ]);
        $logger->debug("testStringableClass: {testStringableClass}", [
            'testStringableClass' => new TestStringableClass()
        ]);
        $logger->debug("testJsonSerializableClass: {testJsonSerializableClass}", [
            'testJsonSerializableClass' => new TestJsonSerializableClass()
        ]);
        $logger->debug("testAttributeClass: {testAttributeClass}", [
            'testAttributeClass' => new TestAttributeClass()
        ]);
        $logger->debug("testRuntimeException: {testRuntimeException}", [
            'testRuntimeException' => new RuntimeException('Error message', 123)
        ]);

        // Scalars as strings
        $this->assertEquals(
            "string: A string",
            $handler->getRecords()[0]->message
        );
        $this->assertEquals(
            "null: null",
            $handler->getRecords()[1]->message
        );
        // Classes (without Stringable) as class names
        $this->assertEquals(
            "dateTime: DateTime",
            $handler->getRecords()[2]->message
        );
        $this->assertEquals(
            "dateTimeZone: DateTimeZone",
            $handler->getRecords()[3]->message
        );
        $this->assertEquals(
            "dateInterval: DateInterval",
            $handler->getRecords()[4]->message
        );
        // Stringable classes as strings
        $this->assertEquals(
            "dataUri: " . __FILE__,
            $handler->getRecords()[5]->message
        );
        $this->assertEquals(
            "uuid: e2abe3e2-059b-3bb4-888a-d84fb5371879",
            $handler->getRecords()[6]->message
        );
        // Test classes, depends on class and interface
        $this->assertEquals(
            "testEnum: Phrity\Monolog\Test\Fixture\TestEnum",
            $handler->getRecords()[7]->message
        );
        $this->assertEquals(
            "testClass: Phrity\Monolog\Test\Fixture\TestClass",
            $handler->getRecords()[8]->message
        );
        $this->assertEquals(
            "testStringableClass: StringableClass",
            $handler->getRecords()[9]->message
        );
        $this->assertEquals(
            "testJsonSerializableClass: Phrity\Monolog\Test\Fixture\TestJsonSerializableClass",
            $handler->getRecords()[10]->message
        );
        $this->assertEquals(
            "testAttributeClass: Phrity\Monolog\Test\Fixture\TestAttributeClass",
            $handler->getRecords()[11]->message
        );
        $this->assertStringStartsWith(
            "testRuntimeException: RuntimeException: Error message",
            $handler->getRecords()[12]->message
        );
    }

    public function testPathInterpolator(): void
    {
        $logger = new Logger('test-path-interpolator');
        $handler = new TestHandler();
        $logger->pushHandler($handler);
        $logger->pushProcessor(new ContextInterpolator());

        $logger->debug("string: {string.unexisting}", [
            'string' => 'A string'
        ]);
        $logger->debug("testEnum: {testEnum.value}", [
            'testEnum' => TestEnum::Yes
        ]);
        $logger->debug("testClass: {testClass.public}", [
            'testClass' => new TestClass()
        ]);
        $logger->debug("testStringableClass: {testStringableClass.public}", [
            'testStringableClass' => new TestStringableClass()
        ]);
        $logger->debug("testJsonSerializableClass: {testJsonSerializableClass.public}", [
            'testJsonSerializableClass' => new TestJsonSerializableClass()
        ]);
        $logger->debug("testAttributeClass: {testAttributeClass.dateTime}", [
            'testAttributeClass' => new TestAttributeClass()
        ]);
        $logger->debug("testRuntimeException: {testRuntimeException.message}", [
            'testRuntimeException' => new RuntimeException('Error message', 123)
        ]);

        // Scalars not resolvable
        $this->assertEquals(
            "string: {string.unexisting}",
            $handler->getRecords()[0]->message
        );
        // Objects can access public properties
        $this->assertEquals(
            "testEnum: yes",
            $handler->getRecords()[1]->message
        );
        $this->assertEquals(
            "testClass: public",
            $handler->getRecords()[2]->message
        );
        $this->assertEquals(
            "testStringableClass: public-stringable",
            $handler->getRecords()[3]->message
        );
        $this->assertEquals(
            "testJsonSerializableClass: public-json-serializeble",
            $handler->getRecords()[4]->message
        );
        $this->assertEquals(
            "testAttributeClass: DateTime",
            $handler->getRecords()[5]->message
        );
        $this->assertEquals(
            "testRuntimeException: {testRuntimeException.message}",
            $handler->getRecords()[6]->message
        );
    }

    public function testSerializerInterpolator(): void
    {
        $logger = new Logger('test-serializer-interpolator');
        $handler = new TestHandler();
        $logger->pushHandler($handler);
        $logger->pushProcessor(new ContextInterpolator(new DefaultSerializer()));

        $logger->debug("string: {string}", [
            'string' => 'A string'
        ]);
        $logger->debug("null: {null}", [
            'null' => null
        ]);
        $logger->debug("dateTime: {dateTime}", [
            'dateTime' => new DateTime('2025-04-11 16:23 UTC')
        ]);
        $logger->debug("dateTimeZone: {dateTimeZone}", [
            'dateTimeZone' => new DateTimeZone('UTC')
        ]);
        $logger->debug("dateInterval: {dateInterval}", [
            'dateInterval' => new DateInterval('P1Y')
        ]);
        $logger->debug("dataUri: {dataUri}", [
            'dataUri' => new SplFileInfo(__FILE__)
        ]);
        $logger->debug("uuid: {uuid}", [
            'uuid' => Uuid::v3(Uuid::fromString(Uuid::NAMESPACE_OID), 'test-default-interpolator')
        ]);
        $logger->debug("testEnum: {testEnum}", [
            'testEnum' => TestEnum::Yes
        ]);
        $logger->debug("testClass: {testClass}", [
            'testClass' => new TestClass()
        ]);
        $logger->debug("testStringableClass: {testStringableClass}", [
            'testStringableClass' => new TestStringableClass()
        ]);
        $logger->debug("testJsonSerializableClass: {testJsonSerializableClass}", [
            'testJsonSerializableClass' => new TestJsonSerializableClass()
        ]);
        $logger->debug("testAttributeClass: {testAttributeClass}", [
            'testAttributeClass' => new TestAttributeClass()
        ]);
        $logger->debug("testClass: {testClass.public}", [
            'testClass' => new TestClass()
        ]);
        $logger->debug("testStringableClass: {testStringableClass.public}", [
            'testStringableClass' => new TestStringableClass()
        ]);
        $logger->debug("testJsonSerializableClass: {testJsonSerializableClass.added}", [
            'testJsonSerializableClass' => new TestJsonSerializableClass()
        ]);
        $logger->debug("testAttributeClass: {testAttributeClass.dateTime}", [
            'testAttributeClass' => new TestAttributeClass()
        ]);

        // Scalars as strings
        $this->assertEquals(
            "string: A string",
            $handler->getRecords()[0]->message
        );
        $this->assertEquals(
            "null: null",
            $handler->getRecords()[1]->message
        );
        // Classes by normalizer
        $this->assertEquals(
            "dateTime: 2025-04-11T16:23:00+00:00",
            $handler->getRecords()[2]->message
        );
        $this->assertEquals(
            "dateTimeZone: UTC",
            $handler->getRecords()[3]->message
        );
        $this->assertEquals(
            "dateInterval: P1Y0M0DT0H0M0S",
            $handler->getRecords()[4]->message
        );
        // Stringable classes by normalizer
        $this->assertStringStartsWith(
            "dataUri: data:application/octet-stream;base64,",
            $handler->getRecords()[5]->message
        );
        $this->assertEquals(
            "uuid: e2abe3e2-059b-3bb4-888a-d84fb5371879",
            $handler->getRecords()[6]->message
        );
        // Test classes by normalizer, depends on class and interface
        $this->assertEquals(
            "testEnum: yes",
            $handler->getRecords()[7]->message
        );
        $this->assertEquals(
            "testClass: Phrity\Monolog\Test\Fixture\TestClass",
            $handler->getRecords()[8]->message
        );
        $this->assertEquals(
            "testStringableClass: StringableClass",
            $handler->getRecords()[9]->message
        );
        $this->assertEquals(
            "testJsonSerializableClass: Phrity\Monolog\Test\Fixture\TestJsonSerializableClass",
            $handler->getRecords()[10]->message
        );
        $this->assertEquals(
            "testAttributeClass: Phrity\Monolog\Test\Fixture\TestAttributeClass",
            $handler->getRecords()[11]->message
        );
        // Test classes by normalizer,using path
        $this->assertEquals(
            "testClass: public",
            $handler->getRecords()[12]->message
        );
        $this->assertEquals(
            "testStringableClass: public-stringable",
            $handler->getRecords()[13]->message
        );
        $this->assertEquals(
            "testJsonSerializableClass: added",
            $handler->getRecords()[14]->message
        );
        $this->assertEquals(
            "testAttributeClass: 2025.04.11",
            $handler->getRecords()[15]->message
        );
    }
}
