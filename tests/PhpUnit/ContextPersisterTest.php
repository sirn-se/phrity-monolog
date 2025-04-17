<?php

declare(strict_types=1);

namespace Phrity\Monolog\Test\PhpUnit;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Phrity\Monolog\Processor\ContextPersister;

class ContextPersisterTest extends TestCase
{
    public function testPersister(): void
    {
        $logger = new Logger('test-persister');
        $handler = new TestHandler();
        $persister = new ContextPersister(['a' => 1]);
        $logger->pushHandler($handler);
        $logger->pushProcessor($persister);

        // Dnitialized a
        $logger->debug('test-persister', ['x' => 1]);
        $this->assertEquals([
            'a' => 1,
            'x' => 1,
        ], $handler->getRecords()[0]->context);

        // Replace a, add b
        $persister->add(['a' => 2, 'b' => 1]);
        $logger->debug('test-persister', ['x' => 2]);
        $this->assertEquals([
            'a' => 2,
            'b' => 1,
            'x' => 2,
        ], $handler->getRecords()[1]->context);

        // Replace all
        $persister->set(['c' => 1]);
        $logger->debug('test-persister', ['x' => 3]);
        $this->assertEquals([
            'c' => 1,
            'x' => 3,
        ], $handler->getRecords()[2]->context);

        // Overwrite with local
        $logger->debug('test-persister', ['c' => 4]);
        $this->assertEquals([
            'c' => 4,
        ], $handler->getRecords()[3]->context);

        // Reset
        $persister->reset();
        $logger->debug('test-persister', ['x' => 4]);
        $this->assertEquals([
            'x' => 4,
        ], $handler->getRecords()[4]->context);
    }
}
